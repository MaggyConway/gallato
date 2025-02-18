<?php
use Bitrix\Main,
	Bitrix\Sale,
	Bitrix\Currency,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader,
	Bitrix\Sale\PaySystem,
	Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);


class VbcherepanovBonusPay extends \CBitrixComponent
{
	/** @var  Main\ErrorCollection $errorCollection*/
	protected $errorCollection;

	/**
	 * @var Sale\Registry registry
	 */
	protected $registry = null;


	public function onPrepareComponentParams($params)
	{
		global $APPLICATION;

		$this->checkModules();

		$this->errorCollection = new Main\ErrorCollection();

		if ((!isset($params["VAR"]) || strlen($params["VAR"])<=0))
		{
			$params["VAR"] = "buyBonus";
		}

		if (!isset($params["REFRESHED_COMPONENT_MODE"]))
		{
			$params["REFRESHED_COMPONENT_MODE"] = "Y";
		}


		if (!isset($params['PERSON_TYPE']))
		{
			$params['PERSON_TYPE'] = 1;
		}

		if (strlen($params["PATH_TO_PAYMENT"]) <= 0)
		{
			$params["PATH_TO_PAYMENT"] = "/personal/order/payment";
		}
		else
		{
			$params["PATH_TO_PAYMENT"] = trim($params["PATH_TO_PAYMENT"]);
		}

		if (strlen($params["PATH_TO_BASKET"]) <= 0)
		{
			$params["PATH_TO_BASKET"] = "/personal/cart";
		}
		else
		{
			$params["PATH_TO_BASKET"] = trim($params["PATH_TO_BASKET"]);
		}

		if (empty($params['SELL_CURRENCY']))
		{
			$params['SELL_CURRENCY'] = Sale\Internals\SiteCurrencyTable::getSiteCurrency(SITE_ID);
		}
		else
		{
			$params["SELL_CURRENCY"] = trim($params["SELL_CURRENCY"]);
		}

		if ($params["REFRESHED_COMPONENT_MODE"] === "Y")
		{
			if (!isset($params["SELL_USER_INPUT"]))
			{
				$params["SELL_USER_INPUT"] = "Y";
			}

			if (strlen($params["PRODUCT_PROVIDER_CLASS"])<=0)
			{
				$params["PRODUCT_PROVIDER_CLASS"] = "\\ITRound\\Vbchbbonus\\ProviderBonusPay";
			}

			if (isset($params['SELL_TOTAL']))
			{
				$params['SELL_TOTAL'] = array_diff($params['SELL_TOTAL'], array(''));
			}

			if (!isset($params["ELIMINATED_PAY_SYSTEMS"]) && !is_array($params["ELIMINATED_PAY_SYSTEMS"]))
			{
				$params["ELIMINATED_PAY_SYSTEMS"] = array();
			}

			$params["ELIMINATED_PAY_SYSTEMS"][] = PaySystem\Manager::getInnerPaySystemId();

			$params['NAME_CONFIRM_TEMPLATE'] = 'confirm_template';

			$params["TEMPLATE_PATH"] = $this->getTemplateName();
		}
		else
		{
			if (strlen($params["CALLBACK_NAME"])<=0)
			{
				$params["CALLBACK_NAME"] = "PayBonusAccountDeliveryOrderCallback";
			}

			if ($params["REDIRECT_TO_CURRENT_PAGE"]=="Y")
			{
				$params["CURRENT_PAGE"] = htmlspecialcharsEx($APPLICATION->GetCurPageParam());
			}
		}

		return $params;
	}

	/**
	 * Check Required Modules
	 * @throws Main\SystemException
	 * @return bool
	 */
	protected function checkModules()
	{
        if (!Loader::includeModule('vbcherepanov.bonus'))
        {
            $this->errorCollection->setError(new Main\Error(Loc::getMessage('VBCHBB_MODULE_NOT_INSTALL')));
            return false;
        }
		if (!Loader::includeModule('sale'))
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SAP_MODULE_NOT_INSTALL')));
			return false;
		}
		return true;
	}

	/**
	 * Prepare data to render in old version of component.
	 * @return void
	 */
	protected function fillArrayResultOld()
	{
		global $APPLICATION;

		$amountArray = unserialize(Main\Config\Option::get("sale", "pay_amount"));

		if (empty($amountArray))
		{
			$amountArray = array (
				array ('AMOUNT' => 10,'CURRENCY' => 'EUR'),
				array ('AMOUNT' => 20,'CURRENCY' => 'EUR'),
				array ('AMOUNT' => 30,'CURRENCY' => 'EUR'),
				array ('AMOUNT' => 40,'CURRENCY' => 'EUR')
			);
		}
		
		if (empty($this->arParams["SELL_AMOUNT"]))
		{
			$this->arResult["PAY_ACCOUNT_AMOUNT"] = $amountArray;
		}
		else
		{
			foreach ($this->arParams["SELL_AMOUNT"] as $val)
			{
				if (!empty($amountArray[$val]))
				{
					$this->arResult["PAY_ACCOUNT_AMOUNT"][$val] = $amountArray[$val];
				}
			}
		}

		foreach ($this->arResult["PAY_ACCOUNT_AMOUNT"] as $key => $value)
		{
			$tmp = $value;
			if (strlen($this->arParams["SELL_CURRENCY"]) > 0)
			{
				if ($value["CURRENCY"] != $this->arParams["SELL_CURRENCY"])
				{
					$tmp = array("AMOUNT" => CCurrencyRates::ConvertCurrency($value["AMOUNT"], $value["CURRENCY"], $this->arParams["SELL_CURRENCY"]), "CURRENCY" => $this->arParams["SELL_CURRENCY"]);
				}
			}
			$this->arResult["AMOUNT_TO_SHOW"][$key] = array(
				"ID" => $key,
				"NAME" => SaleFormatCurrency($tmp["AMOUNT"], $tmp["CURRENCY"]),
				"LINK" => $APPLICATION->GetCurPageParam($this->arParams["VAR"]."=".$key, array("buyBonus"))
			);
		}
	}

	/**
	 * Get list of paysystems by user's type
	 * @return array
	 */
	protected function getListPaySystems()
	{
		$minimumValue = 1;

		/** @var \Bitrix\Sale\Order $order */
		$order = null;

		$basket = $this->initBasket($minimumValue, false);

		$order = $this->initOrder($basket, $this->arParams['PERSON_TYPE']);

		$paymentCollection = $order->getPaymentCollection();
		/** @var \Bitrix\Sale\Payment $payment */
		$payment = $paymentCollection->createItem();

		return PaySystem\Manager::getListWithRestrictions($payment);
	}

	/**
	 * Prepare data to render in new version of component.
	 * @return void
	 */
	protected function fillArrayResult()
	{
		global $USER;

		if (!$USER->IsAuthorized())
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SALE_ACCESS_DENIED')));
			return;
		}

		if ($this->arParams["SELL_VALUES_FROM_VAR"] === "Y")
		{
			if (!empty($this->arParams['SELL_VAR_PRICE_VALUE']))
			{
				$this->arResult['SELL_VAR_PRICE_VALUE'] = (float)($this->arParams['SELL_VAR_PRICE_VALUE']);
			}
			else
			{
				$this->errorCollection->setError(new Main\Error(Loc::getMessage("SAP_EMPTY_VAR_PRICE_VALUE")));
			}
		}

		$paySystemList = $this->getListPaySystems();

		foreach ($paySystemList as $paySystemElement)
		{

			if (!in_array($paySystemElement['ID'], $this->arParams['ELIMINATED_PAY_SYSTEMS']) && $paySystemElement['ACTION_FILE']!='innerbonus')
			{
				if (!empty($paySystemElement["LOGOTIP"]))
				{
					$paySystemElement["LOGOTIP"] = CFile::GetFileArray($paySystemElement['LOGOTIP']);
					$fileTemp = CFile::ResizeImageGet(
						$paySystemElement["LOGOTIP"]["ID"],
						array("width" => "95", "height" =>"55"),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						true
					);
					$paySystemElement["LOGOTIP"] = $fileTemp["src"];
				}

				$this->arResult['PAYSYSTEMS_LIST'][] = $paySystemElement;
			}
		}

		if (empty($this->arResult['PAYSYSTEMS_LIST']))
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage("SAP_EMPTY_PAY_SYSTEM_LIST")));
		}

		if ($this->errorCollection->isEmpty())
		{
			$currencyList = CCurrencyLang::GetFormatDescription($this->arParams["SELL_CURRENCY"]);
			$this->arResult['FORMATED_CURRENCY'] = $currencyList['FORMAT_STRING'];

			$signer = new Main\Security\Sign\Signer;
			$ajaxParams = array(
				'PERSON_TYPE' => (int)$this->arParams['PERSON_TYPE'],
				'SELL_CURRENCY' => $this->arParams['SELL_CURRENCY'],
				'NAME_CONFIRM_TEMPLATE' => $this->arParams['NAME_CONFIRM_TEMPLATE'],
				'PATH_TO_PAYMENT' => $this->arParams['PATH_TO_PAYMENT'],
                'USE_COUPONE' =>$this->arParams['USE_COUPONE'],
            );
			$this->arResult['SIGNED_PARAMS'] = base64_encode($signer->sign(serialize($ajaxParams), 'vbcherepanov.bonus.pay'));
		}
	}

	public function executeComponent()
	{
		global $APPLICATION;
		$templateName = null;
		if ($this->checkModules())
		{
			/** @var Main\HttpRequest $request */
			$request = Application::getInstance()->getContext()->getRequest();
			$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

			if ($this->arParams["SET_TITLE"]!="N")
			{
				$APPLICATION->SetTitle(Loc::getMessage('SAP_TITLE'));
			}

			$this->setRegistry();
			if ($this->arParams['AJAX_DISPLAY'] === 'Y')
			{
				$this->orderPayment($request);
				$templateName = $this->arParams['NAME_CONFIRM_TEMPLATE'];
			}
			else
			{
				if ($this->arParams['REFRESHED_COMPONENT_MODE'] === 'N')
				{
					$this->fillArrayResultOld();

					if (strlen($request[$this->arParams["VAR"]]) > 0)
					{
						$this->sendToBasketOld($request);
					}
				}
				else
				{
					$this->fillArrayResult();
				}
			}
		}

		$this->formatResultErrors();
		$this->includeComponentTemplate($templateName);
	}

	/**
	 * Return current class registry
	 *
	 * @param mixed[] array that date conversion performs in
	 * @return void
	 */
	protected function setRegistry()
	{
		$this->registry = Sale\Registry::getInstance(Sale\Order::getRegistryType());
	}

	/**
	 * Move all errors to $this->arResult, if there were any
	 * @return void
	 */
	protected function formatResultErrors()
	{
		if (!$this->errorCollection->isEmpty())
		{
			/** @var Main\Error $error */
			foreach ($this->errorCollection->toArray() as $error)
			{
				$this->arResult['errorMessage'][] = $error->getMessage();
			}
		}
	}

	/**
	 * Initialize empty shipment of order
	 * @param Sale\Order $order
	 * @return void
	 */
	protected function initOrderShipment($order)
	{
		$shipmentCollection = $order->getShipmentCollection();
		$shipment = $shipmentCollection->createItem();
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		foreach ($order->getBasket() as $item)
		{
			/** @var \Bitrix\Sale\ShipmentItem $shipmentItem */
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity(1);
		}
		$emptyDeliveryServiceId = Sale\Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId();
		$shipment->setField('DELIVERY_ID', $emptyDeliveryServiceId);
	}

	/**
	 * Initialize new basket with payment value
	 * @param float $requestValue
	 * @param bool $savePropertyCharge
	 * @return Sale\Basket $basket
	 */
	protected function initBasket($requestValue, $savePropertyCharge = true)
	{
		$productId = (int)($requestValue * 100);
		$basketClassName = $this->registry->getBasketClassName();
		/** @var Sale\Basket $basket */
		$basket = $basketClassName::create(SITE_ID);
        $BonusCore=new ITRound\Vbchbbonus\Vbchbbcore();
		$basketItem = $basket->createItem('sale', $productId);
        if($this->arParams['USE_COUPONE']=='Y') {
            $mask = $BonusCore->GetOptions(SITE_ID, 'COUPONE_MASK');
            $mask = $mask['OPTION'];
            if(trim($mask)=='') $mask = 'X-[A][9][a]-[9][9][9]-[X]';
            $coupone = \ITRound\Vbchbbonus\CouponTable::GenerateCoupon(true, $mask);
        }
		$productFields = array(
			"BASE_PRICE" => $requestValue,
			"CURRENCY" => $this->arParams["SELL_CURRENCY"],
			"QUANTITY" => 1,
			"LID" => SITE_ID,
			"DELAY" => "N",
			"CAN_BUY" => "Y",
			"NAME" => str_replace("#SUM#", SaleFormatCurrency($requestValue, $this->arParams["SELL_CURRENCY"]), Loc::getMessage((($this->arParams['USE_COUPONE']!='Y') ? "SAP_BASKET_NAME" : 'SAP_BASKET_NAME_COUPONE'), array('#NAME#' => 'NAME')) . $coupone),
			"PRODUCT_PROVIDER_CLASS" => $this->arParams["PRODUCT_PROVIDER_CLASS"]
		);

		$result = $basketItem->setFields($productFields);
		if (!$result->isSuccess())
		{
			$this->errorCollection->add($result->getErrors());
		}

		$propertyCollection = $basketItem->getPropertyCollection();

		if ($savePropertyCharge)
		{
			/** @var Sale\BasketPropertyItem $propertyItem */

			if($this->arParams['USE_COUPONE']=='Y'){
                $prop_name = 'SUM_OF_COUPONE';
                $propertyItem = $propertyCollection->createItem();
                $result = $propertyItem->setFields(array('NAME'=>'COUPONE_CODE','CODE'=>'COUPONE_CODE','VALUE' => $coupone, 'SORT'=>200));
                if (!$result->isSuccess())
                {
                    $this->errorCollection->add($result->getErrors());
                }
            }else{
                $prop_name = 'SUM_OF_CHARGE';
            }

			$propertyItem = $propertyCollection->createItem();
			$result = $propertyItem->setFields(array('NAME'=>$prop_name,'CODE'=>$prop_name,'VALUE' => $requestValue, 'SORT'=>100));
			if (!$result->isSuccess())
			{
				$this->errorCollection->add($result->getErrors());
			}
		}

		return $basket;
	}

	/**
	 * Initialize new order
	 * @param Sale\Basket $basket
	 * @param int $personType
	 * @return Sale\Order $order
	 */
	protected function initOrder($basket, $personType)
	{
		global $USER;

		$orderClassName = $this->registry->getOrderClassName();
		/** @var Sale\Order $order */
		$order = $orderClassName::create(SITE_ID, $USER->GetID(), $this->arParams['SELL_CURRENCY']);

		/** @var Main\Result $result */
		$result = $order->setBasket($basket);
		if (!$result->isSuccess())
		{
			$this->errorCollection->add($result->getErrors());
		}

		$result = $order->setPersonTypeId($personType);
		if (!$result->isSuccess())
		{
			$this->errorCollection->add($result->getErrors());
		}

		$this->initOrderShipment($order);

		if (
			isset($this->arParams['CONTEXT_SITE_ID'])
			&& $this->arParams['CONTEXT_SITE_ID'] > 0
			&& Loader::includeModule('landing')
		)
		{
			$code = \Bitrix\Sale\TradingPlatform\Landing\Landing::getCodeBySiteId($this->arParams['CONTEXT_SITE_ID']);

			$platform = \Bitrix\Sale\TradingPlatform\Landing\Landing::getInstanceByCode($code);
			if ($platform->isInstalled())
			{
				$collection = $order->getTradeBindingCollection();
				$collection->createItem($platform);
			}
		}

		return $order;
	}

	/**
	 * Sending value payment to basket in old version component. Made for compatibility
	 * @param Main\HttpRequest $request
	 * @return void
	 */
	protected function sendToBasketOld($request)
	{
		$baseCurrency = CCurrency::GetBaseCurrency();

		$productId = (int)($request[$this->arParams["VAR"]]);

		if (!empty($this->arResult["PAY_ACCOUNT_AMOUNT"][$productId]))
		{
			$price = $this->arResult["PAY_ACCOUNT_AMOUNT"][$productId]["AMOUNT"];
			$currency = $this->arResult["PAY_ACCOUNT_AMOUNT"][$productId]["CURRENCY"];

			if ($currency != $baseCurrency)
			{
				$price = CCurrencyRates::ConvertCurrency($price, $currency, $baseCurrency);
				$currency = $baseCurrency;
			}

			$fields = array(
				"PRODUCT_ID" => $productId,
				"BASE_PRICE" => $price,
				"CURRENCY" => $currency,
				"QUANTITY" => 1,
				"LID" => SITE_ID,
				"DELAY" => "N",
				"CAN_BUY" => "Y",
				"NAME" => str_replace("#SUM#", $this->arResult["AMOUNT_TO_SHOW"][$productId]["NAME"], Loc::getMessage("SAP_BASKET_NAME")),
				"PAY_CALLBACK_FUNC" => $this->arParams["CALLBACK_NAME"]
			);

			$basketClassName = $this->registry->getBasketClassName();
			/** @var Sale\Basket $basket */
			$basket = $basketClassName::loadItemsForFUser(Sale\Fuser::getId(), Main\Context::getCurrent()->getSite());

			$item = $basket->getExistsItem('sale', $productId);
			if ($item)
			{
				$resultOldAdd = $item->setField('QUANTITY', $item->getQuantity() + 1);
			}
			else
			{
				$item = $basket->createItem('sale', $productId);
				$resultOldAdd = $item->setFields($fields);
			}

			if ($resultOldAdd->isSuccess())
			{
				$resultSavingBasket = $basket->save();
				if ($resultSavingBasket->isSuccess())
				{
					if (CModule::IncludeModule("statistic"))
					{
						$statistic = new \CStatistic;
						$statistic->Set_Event("sale2basket", "sale", $productId);
					}

					if ($this->arParams["REDIRECT_TO_CURRENT_PAGE"] == "Y")
					{
						LocalRedirect($this->arResult["CURRENT_PAGE"]);
					}
					else
					{
						LocalRedirect($this->arParams["PATH_TO_BASKET"]);
					}
				}
				else
				{
					$this->errorCollection->setError(new Main\Error(Loc::getMessage("SAP_ERROR_ADD_BASKET")));
					$this->errorCollection->add($resultSavingBasket->getErrors());
				}
			}
			else
			{
				$this->errorCollection->setError(new Main\Error(Loc::getMessage("SAP_ERROR_ADD_BASKET")));
				$this->errorCollection->add($resultOldAdd->getErrors());
			}
		}
		else
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage("SAP_WRONG_ID")));
		}
	}
	
	/**
	 * Preparing data for ordering payment
	 * @param Main\HttpRequest $request
	 * @return bool $result
	 */
	protected function checkRequestData($request)
	{
		$result = true;

		if (!check_bitrix_sessid())
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SAP_INVALID_TOKEN')));
			return false;
		}

		if ((float)($request["buyBonus"]) <= 0)
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SAP_WRONG_INPUT_VALUE')));
			$result = false;
		}

		if (empty($request['paySystemId']))
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SAP_EMPTY_PAY_SYSTEM_ID')));
			$result = false;
		}
		if($request['useCoupone'] == 'true')
		    $this->arParams['USE_COUPONE'] = 'Y';
        if($request['useCoupone'] == 'false')
            $this->arParams['USE_COUPONE'] = 'N';


		if ($this->arParams['SELL_USER_INPUT'] === 'N'
			&& $this->arParams['SELL_VALUES_FROM_VAR'] !== 'Y'
			&& !in_array($request["buyBonus"], $this->arParams['SELL_TOTAL']))
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SAP_IN_NOT_ARRAY_VALUES')));
			$result = false;
		}

		return $result;
	}

	/**
	 * Ordering payment for calling in ajax callback
	 * @param Main\HttpRequest $request			Params were selected in template.
	 * @return void
	 */
	protected function orderPayment($request)
	{
		global $USER;

		if (!$USER->IsAuthorized())
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SALE_ACCESS_DENIED')));
			return;
		}

		if (!$this->checkRequestData($request))
		{
			return;
		}

		$valuePayment = (float)($request["buyBonus"]);

		$basket = $this->initBasket($valuePayment);

		if (!$basket)
		{
			return;
		}
		/** @var \Bitrix\Sale\Order $order */
		$order = $this->initOrder($basket, (int)$this->arParams['PERSON_TYPE']);

		if (!$order)
		{
			return;
		}

		$paySystemObject  = PaySystem\Manager::getObjectById((int)$request['paySystemId']);
		if (empty($paySystemObject))
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SAP_ERROR_ORDER_PAYMENT_SYSTEM')));
			return;
		}

		$paymentCollection = $order->getPaymentCollection();

		/** @var \Bitrix\Sale\Payment $payment */
		$payment = $paymentCollection->createItem();

		$paymentResult = $payment->setFields(array(
			'SUM' => $order->getPrice(),
			'CURRENCY'=> $this->arParams['SELL_CURRENCY'],
			'PAY_SYSTEM_ID' => $paySystemObject->getField('ID'),
			'PAY_SYSTEM_NAME' => $paySystemObject->getField('NAME')
		));

		if (!$paymentResult->isSuccess())
		{
			$this->errorCollection->add($paymentResult->getErrors());
			return;
		}

		if ($firstProfileId = Sale\OrderUserProperties::getFirstId($order->getPersonTypeId(), $order->getUserId()))
		{
			$resultUserProperties = Sale\Internals\UserPropsValueTable::getList(
				array(
					'filter' => array("=USER_PROPS_ID" => $firstProfileId),
					'select' => array("ORDER_PROPS_ID", "VALUE")
				)
			);
			while ($userProperty = $resultUserProperties->fetch())
			{
				$propertiesValueList[$userProperty["ORDER_PROPS_ID"]] = $userProperty['VALUE'];
			}

			if (!empty($propertiesValueList) && is_array($propertiesValueList))
			{
				$propertyCollection = $order->getPropertyCollection();
				/** @var Sale\PropertyValue $property */
				foreach ($propertyCollection as $property)
				{
					$propertyOrderId = (int)($property->getField('ORDER_PROPS_ID'));

					if (!empty($propertiesValueList[$propertyOrderId]))
					{
						$property->setValue($propertiesValueList[$propertyOrderId]);
					}
				}
			}
		}

//		$affiliateID = CSaleAffiliate::GetAffiliate();
//
//		if ($affiliateID > 0)
//		{
//			$affiliateBase = CSaleAffiliate::GetList(array(), array("SITE_ID" => Main\Context::getCurrent()->getSite(), "ID" => $affiliateID));
//			$affiliates = $affiliateBase->Fetch();
//			if (count($affiliates) > 1)
//				$order->setField('AFFILIATE_ID', $affiliateID);
//		}

		$resultSaving = $order->save();

		if ($resultSaving->isSuccess())
		{
			if (Loader::includeModule("statistic"))
			{
				$statistic = new \CStatistic;
				$statistic->Set_Event("sale2basket", "sale", $valuePayment);
			}

			$values = $paySystemObject->getFieldsValues();

			$this->arResult = array(
				"ORDER_ID"=>$order->getId(),
				"ORDER_DATE"=>$order->getDateInsert()->toString(),
				"PAYMENT_ID"=>$payment->getId(),
				"IS_CASH" => $paySystemObject->isCash() || $paySystemObject->getField("ACTION_FILE") === 'cash',
				"NAME_CONFIRM_TEMPLATE"=>$this->arParams['NAME_CONFIRM_TEMPLATE']
			);

			if ($values['NEW_WINDOW'] === 'Y')
			{
				$this->arResult["PAYMENT_LINK"] = $this->arParams['PATH_TO_PAYMENT']."/?ORDER_ID=".$order->getField("ACCOUNT_NUMBER")."&PAYMENT_ID=".$payment->getId();
			}
			else
			{
				$paySystemBufferedOutput = $paySystemObject->initiatePay($payment, null, PaySystem\BaseServiceHandler::STRING);
				if ($paySystemBufferedOutput->isSuccess())
				{
					$this->arResult["TEMPLATE"] = $paySystemBufferedOutput->getTemplate();
				}
				else
				{
					$this->errorCollection->add($paySystemBufferedOutput->getErrors());
				}
			}
		}
		else
		{
			$this->errorCollection->add($resultSaving->getErrors());
		}
	}
}