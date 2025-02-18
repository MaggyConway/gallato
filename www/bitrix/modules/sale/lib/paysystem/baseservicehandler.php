<?php
namespace Bitrix\Sale\PaySystem;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Text;
use Bitrix\Main\Error;
use Bitrix\Main\IO;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Payment;

Loc::loadMessages(__FILE__);

abstract class BaseServiceHandler
{
	const STREAM = 1;
	const STRING = 2;

	const TEST_URL = 'test';
	const ACTIVE_URL = 'active';

	protected $handlerType = '';

	protected $service = null;

	protected $extraParams = array();
	protected $initiateMode = self::STREAM;

	/** @var bool */
	protected $isClone = false;

	/**
	 * @param Payment $payment
	 * @param Request|null $request
	 * @return ServiceResult
	 */
	abstract public function initiatePay(Payment $payment, Request $request = null);

	/**
	 * BaseServiceHandler constructor.
	 * @param $type
	 * @param Service $service
	 */
	public function __construct($type, Service $service)
	{
		$this->handlerType = $type;
		$this->service = $service;
	}

	/**
	 * @param Payment|null $payment
	 * @param string $template
	 * @return ServiceResult
	 */
	public function showTemplate(Payment $payment = null, $template = '')
	{
		$result = new ServiceResult();

		global $APPLICATION, $USER, $DB;

		$templatePath = $this->searchTemplate($template);

		if ($templatePath != '' && IO\File::isFileExists($templatePath))
		{
			$params = array_merge($this->getParamsBusValue($payment), $this->getExtraParams());

			if ($this->initiateMode == self::STREAM)
			{
				require($templatePath);

				if ($this->service->getField('ENCODING') != '')
				{
					define("BX_SALE_ENCODING", $this->service->getField('ENCODING'));
					AddEventHandler('main', 'OnEndBufferContent', array($this, 'OnEndBufferContent'));
				}
			}
			elseif ($this->initiateMode == self::STRING)
			{
				ob_start();
				$content = require($templatePath);

				$buffer = ob_get_contents();
				if (strlen($buffer) > 0)
					$content = $buffer;

				if ($this->service->getField('ENCODING') != '')
				{
					$encoding = Context::getCurrent()->getCulture()->getCharset();
					$content = Text\Encoding::convertEncoding($content, $encoding, $this->service->getField('ENCODING'));
				}

				$result->setTemplate($content);
				ob_end_clean();
			}
		}
		else
		{
			$result->addError(new Error(Loc::getMessage('SALE_PS_BASE_SERVICE_TEMPLATE_ERROR')));
		}

		return $result;
	}

	/**
	 * @param string $template
	 * @return string
	 */
	private function searchTemplate($template)
	{
		$documentRoot = Application::getDocumentRoot();
		$siteTemplate = \CSite::GetCurTemplate();
		$template = Manager::sanitize($template);
		$handlerName = static::getName();

		$folders = array();

		$folders[] = '/local/templates/'.$siteTemplate.'/payment/'.$handlerName.'/template';
		if ($siteTemplate !== '.default')
			$folders[] = '/local/templates/.default/payment/'.$handlerName.'/template';

		$folders[] = '/bitrix/templates/'.$siteTemplate.'/payment/'.$handlerName.'/template';
		if ($siteTemplate !== '.default')
			$folders[] = '/bitrix/templates/.default/payment/'.$handlerName.'/template';

		$baseFolders = Manager::getHandlerDirectories();
		$folders[] = $baseFolders[$this->handlerType].$handlerName.'/template';

		foreach ($folders as $folder)
		{
			$templatePath = $documentRoot.$folder.'/'.$template.'.php';

			if (IO\File::isFileExists($templatePath))
				return $templatePath;
		}

		return '';
	}


	/**
	 * @param Payment $payment
	 * @return array
	 */
	public function getParamsBusValue(Payment $payment = null)
	{
		$params = array();
		$codes = $this->getBusinessCodes();

		if ($codes)
		{
			foreach ($codes as $code)
				$params[$code] = $this->getBusinessValue($payment, $code);
		}

		return $params;
	}

	/**
	 * @return mixed|string
	 */
	static protected function getName()
	{
		return Manager::getFolderFromClassName(get_called_class());
	}

	/**
	 * @param Payment $payment
	 * @param $code
	 * @return mixed
	 */
	protected function getBusinessValue(Payment $payment = null, $code)
	{
		$value = BusinessValue::getValueFromProvider($payment, $code, $this->service->getConsumerName());
		if (is_string($value))
		{
			$value = trim($value);
		}

		return $value;
	}

	/**
	 * @return array
	 */
	public function getDescription()
	{
		$data = array();
		$documentRoot = Application::getDocumentRoot();
		$dirs = Manager::getHandlerDirectories();
		$handlerDir = $dirs[$this->handlerType];
		$file = $documentRoot.$handlerDir.static::getName().'/.description.php';

		if (IO\File::isFileExists($file))
		{
			require $file;
		}

		if (isset($data["CODES"]) && is_array($data["CODES"]))
		{
			$data["CODES"] = $this->filterDescriptionCodes($data["CODES"]);
		}

		return $data;
	}

	/**
	 * @param $codes
	 * @return array
	 */
	protected function filterDescriptionCodes($codes)
	{
		$psMode = $this->service->getField("PS_MODE");
		return array_filter($codes, static function ($code) use ($psMode) {
			if (!isset($code["HANDLER_MODE"]))
			{
				return true;
			}

			if (isset($code["HANDLER_MODE"]) && !is_array($code["HANDLER_MODE"]))
			{
				trigger_error("HANDLER_MODE must be an array", E_USER_WARNING);
				return false;
			}

			return in_array($psMode, $code["HANDLER_MODE"], true);
		});
	}

	/**
	 * @return array
	 */
	protected function getBusinessCodes()
	{
		static $data = array();

		if (!$data)
		{
			$result = $this->getDescription();
			if ($result['CODES'])
				$data = array_keys($result['CODES']);
		}

		return $data;
	}

	/**
	 * @return array
	 */
	protected function getExtraParams()
	{
		return $this->extraParams;
	}

	/**
	 * @param array $values
	 */
	public function setExtraParams(array $values)
	{
		$this->extraParams = $values;
	}

	/**
	 * @return array
	 */
	public abstract function getCurrencyList();

	/**
	 * @param Payment $payment
	 * @return ServiceResult
	 */
	public function creditNoDemand(Payment $payment)
	{
		return new ServiceResult();
	}

	/**
	 * @param Payment $payment
	 * @return ServiceResult
	 */
	public function debitNoDemand(Payment $payment)
	{
		return new ServiceResult();
	}

	/**
	 * @return array
	 */
	public static function getHandlerModeList()
	{
		return array();
	}

	/**
	 * @param int $mode
	 */
	public function setInitiateMode($mode)
	{
		$this->initiateMode = $mode;
	}

	/**
	 * @param Payment $payment
	 * @param string $action
	 * @return string
	 */
	protected function getUrl(Payment $payment = null, $action)
	{
		$urlList = $this->getUrlList();
		if (isset($urlList[$action]))
		{
			$url = $urlList[$action];

			if (is_array($url))
			{
				if ($this->isTestMode($payment) && isset($url[self::TEST_URL]))
					return $url[self::TEST_URL];
				else
					return $url[self::ACTIVE_URL];
			}
			else
			{
				return $url;
			}
		}

		return '';
	}

	/**
	 * @param Payment $payment
	 * @return bool
	 */
	protected function isTestMode(Payment $payment = null)
	{
		return false;
	}

	/**
	 * @return array
	 */
	protected function getUrlList()
	{
		return array();
	}


	/**
	 * @param \SplObjectStorage $cloneEntity
	 *
	 * @return BaseServiceHandler
	 */
	public function createClone(\SplObjectStorage $cloneEntity)
	{
		if ($this->isClone() && $cloneEntity->contains($this))
		{
			return $cloneEntity[$this];
		}

		$serviceHandlerClone = clone $this;
		$serviceHandlerClone->isClone = true;

		if (!$cloneEntity->contains($this))
		{
			$cloneEntity[$this] = $serviceHandlerClone;
		}

		if ($this->service)
		{
			if ($cloneEntity->contains($this->service))
			{
				$serviceHandlerClone->service = $cloneEntity[$this->service];
			}
		}

		return $serviceHandlerClone;
	}

	/**
	 * @return bool
	 */
	public function isClone()
	{
		return $this->isClone;
	}

	/**
	 * @return string
	 */
	public function getHandlerType()
	{
		return $this->handlerType;
	}

	/**
	 * @param $content
	 */
	public function OnEndBufferContent(&$content)
	{
		global $APPLICATION;
		header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
		$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
		$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
	}

	/**
	 * @return array
	 */
	public function getDemoParams()
	{
		return array();
	}

	/**
	 * @return bool
	 */
	public function isTuned()
	{
		return true;
	}
}