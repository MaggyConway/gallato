<?
namespace ITRound\Vbchbbonus;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class CBonusRestAPI
{
    private $parameter;
    private $DB;
    public $realIp;
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';
    const ROOT_DIR_CONTROLLERS = 'restcontroller';
    private $apiPath;
    private $apiVersion;
    private $access;
    private $controller;
    private $action;
    private $pathParts;
    private $params;

    public function __construct($site)
    {
        if ($site != '') {
            $this->DB = \Bitrix\Main\Application::getConnection();
            $this->parameter = $this->GetParam($site);
            $this->access = true;
            if ($this->checkPathApi() && $this->checkUseApi()) {
                if ($this->checkFilters()) {
                    $this->setHeaders();
                    $this->start();
                } else {
                    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS' && $_SERVER['HTTP_ORIGIN']) {
                        $this->setHeadersPreQuery();
                    } else {

                        $this->DenyAccess();
                    }
                }
                die();
            }
        }
    }

    private function start()
    {
        $this->pathParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

        $this->apiPath = strtolower(current($this->pathParts));
        array_shift($this->pathParts);
        if ($this->checkUseVersion()) {
            if (current($this->pathParts)) {
                $this->apiVersion = strtolower(current($this->pathParts));
                array_shift($this->pathParts);
            }
        }
        if (current($this->pathParts)) {
            $this->controller = strtolower(current($this->pathParts));
            array_shift($this->pathParts);
        }
        if (current($this->pathParts)) {
            $this->action = strtolower(current($this->pathParts));
            array_shift($this->pathParts);
        }
        if ($this->getMethod() == self::GET) {
            $this->getParamsRequestUri();
        } elseif ($this->getMethod() == self::POST) {
            $this->getParamsRequestUri();
            if ($ar = (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ? json_decode(file_get_contents('php://input'), true) : $_POST) {
                foreach ($ar as $param => $value) {
                    $this->pathParts[$param] = $value;
                }
            }
        } elseif (
            (
                $this->getMethod() == self::PUT ||
                $this->getMethod() == self::PATCH ||
                $this->getMethod() == self::DELETE ||
                $this->getMethod() == self::OPTIONS
            ) &&
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        ) {
            $this->getParamsRequestUri();
            if ($ar = json_decode(file_get_contents('php://input'), true)) {
                foreach ($ar as $param => $value) {
                    $this->pathParts[$param] = $value;
                }
            }
        } else {
            $this->BadRequest();
        }

        $this->params = (count($this->pathParts) > 0) ? $this->pathParts : [];
        if ($this->getController() && $this->getAction()) {
            $this->ControllerRun();
        } else {
            $this->BadRequest();
        }
        die();
    }

    private function checkUseVersion()
    {
        return ($this->parameter['USE_VERSIONS'] == 'Y');
    }

    public function getApiPath()
    {
        return $this->apiPath;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function getController()
    {
        return $this->controller;
    }

    private function log()
    {
        if ($this->parameter['SUPPORT_USE_LOG'] == 'Y') {
            \Bitrix\Main\Diag\Debug::writeToFile($this->Requestget(), '', str_replace('.', '-' . date('Y-m-d') . '.', $this->parameter['SUPPORT_LOG_PATH']));
        }
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParameters()
    {
        return $this->params;
    }

    private function getParamsRequestUri()
    {
        $tmp = [];
        $_SERVER['REQUEST_URI'] = str_replace('/?', '?', $_SERVER['REQUEST_URI']);
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            if (strpos($this->action, '?') !== false) {
                $this->action = explode('?', $this->action)[0];
            }
            array_pop($this->pathParts);
            if (count($this->pathParts) > 1) {
                foreach ($this->pathParts as $param => $value) {
                    $tmp['get-parameter-' . $param] = $value;
                }
            } else {
                $tmp = [];
            }
            $this->pathParts = explode('?', $_SERVER['REQUEST_URI']);
            $this->pathParts = ($this->pathParts[1]) ? explode('&', $this->pathParts[1]) : [];
            if ($this->pathParts) {
                foreach ($this->pathParts as $item) {
                    $item = explode('=', $item);
                    $tmp[urldecode($item[0])] = urldecode($item[1]);
                }
                $this->pathParts = $tmp;
            }
        }
    }

    private function ControllerRun()
    {
        $controller = false;
        if ($this->getApiVersion()) {

            if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . self::ROOT_DIR_CONTROLLERS . DIRECTORY_SEPARATOR . $this->getApiVersion() . DIRECTORY_SEPARATOR . strtolower($this->getController()) . '.php')) {
                $this->BadRequest(Loc::getMessage('CLASS_NOT_FOUND', ['#OBJECT#' => ucfirst($this->getController())]));
            } else {
                include_once(__DIR__ . DIRECTORY_SEPARATOR . self::ROOT_DIR_CONTROLLERS . DIRECTORY_SEPARATOR . $this->getApiVersion() . DIRECTORY_SEPARATOR . strtolower($this->getController()) . '.php');
                $controller = __NAMESPACE__ . '\\' . self::ROOT_DIR_CONTROLLERS . '\\' . $this->getApiVersion() . '\\' . ucfirst(strtolower($this->getController()));
            }

        }
        $controllerObject = new $controller($this);
        if (method_exists($controllerObject, $this->getAction())) {
            $this->log();
            $action = $this->getAction();

            $controllerObject->$action();
        } else {
            $this->BadRequest(Loc::getMessage('METHOD_NOT_FOUND', ['#OBJECT#' => ucfirst($this->getController()), '#METHOD#' => $this->getAction()]));
        }
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function checkUseApi()
    {
        return ($this->parameter['USE_RESTAPI'] == 'Y');
    }

    private function checkPathApi()
    {
        $apiPath = trim($this->parameter['PATH_RESTAPI']);
        $currentModule = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[0];
        $apiModule = explode('/', $apiPath)[0];
        return ($currentModule == $apiModule) ? true : false;
    }

    private function GetParam($site = '')
    {
        $default = [
            'USE_VERSIONS' => 'Y',
            'SUPPORT_LOG_PATH' => '/bonus_restapi.log',
            'PATH_RESTAPI' => 'rest',
            'USE_ACCESS_CONTROL_ALLOW_ORIGIN_FILTER' => '',
            'WHITE_LIST_DOMAIN_ACCESS_CONTROL_ALLOW_ORIGIN' => '',
        ];
        $key = [
            'USE_RESTAPI',
            'SUPPORT_USE_LOG',
            'ONLY_HTTPS_EXCHANGE',
            'USE_AUTH_BY_LOGIN_PASSWORD',
            'USE_AUTH_TOKEN',
            'RESTAPI_TOKEN',
            'IDENTUSER',
        ];
        $result = [];
        $BBCORE = new \ITRound\Vbchbbonus\Vbchbbcore();
        if ($site != '') {
            foreach ($key as $kk) {
                $r = $BBCORE->GetOptions($site, $kk);
                $result[$kk] = $r['OPTION'] ? $r['OPTION'] : '';
            }
        }
        unset($BBCORE);
        $k = array_merge($result, $default);
        return $k;
    }

    private function checkFilters()
    {
        $ar = [
            'checkHttps',
            'checkLoginPassword',
            'checkToken',
        ];
        foreach ($ar as $filter) {
            //if ($this->access) {
            $this->$filter();
            //}
        }
        return $this->access;
    }

    private function setHeaders()
    {
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization-Token');
        header('Access-Control-Allow-Origin: ' . $_SERVER['SERVER_NAME']);
        if ($this->parameter['USE_ACCESS_CONTROL_ALLOW_ORIGIN_FILTER'] == 'Y') {
            $ar = $this->parameter['WHITE_LIST_DOMAIN_ACCESS_CONTROL_ALLOW_ORIGIN'];
            if (strpos($ar, '*') !== false) {
                header('Access-Control-Allow-Origin: *');
            } else {
                $ar = explode(';', $ar);
                $ar = array_diff($ar, ['']);
                foreach ($ar as &$item) {
                    $item = trim($item);
                    if ($item == $_SERVER['HTTP_ORIGIN']) {
                        header('Access-Control-Allow-Origin: ' . $item);
                        break;
                    }
                }
            }
        }
    }

    private function setHeadersPreQuery()
    {
        header('HTTP/1.0 200');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization-Token');
        header('Access-Control-Max-Age: 604800'); // 7 days
        if ($this->parameter['USE_ACCESS_CONTROL_ALLOW_ORIGIN_FILTER'] == 'Y') {
            $ar = $this->parameter['WHITE_LIST_DOMAIN_ACCESS_CONTROL_ALLOW_ORIGIN'];
            if (strpos($ar, '*') !== false) {
                header('Access-Control-Allow-Origin: *');
            } else {
                $ar = explode(';', $ar);
                $ar = array_diff($ar, ['']);
                foreach ($ar as &$item) {
                    $item = trim($item);
                    if ($item == $_SERVER['HTTP_ORIGIN']) {
                        header('Access-Control-Allow-Origin: ' . $item);
                        break;
                    }
                }
            }
        }
    }

    public static function checkLibraryAvailability($libraryCode)
    {
        return array_search(strtolower($libraryCode), get_loaded_extensions()) ? true : false;
    }


    public function generateTokens($lenght = 32)
    {
        $key = '';
        if (function_exists('openssl_random_pseudo_bytes')) {
            $key = base64_encode(openssl_random_pseudo_bytes($lenght));
        } else {
            $key = bin2hex(random_bytes($lenght));
        }
        return $key;
    }

    private function checkLoginPassword()
    {
        if ($this->parameter['USE_AUTH_BY_LOGIN_PASSWORD'] == 'Y') {
            $this->access = false;
            $ar = \Bitrix\Main\UserTable::getList(
                [
                    'filter' => ['LOGIN' => trim($_SERVER['HTTP_AUTHORIZATION_LOGIN'])],
                    'select' => ['ID', 'PASSWORD']
                ]
            )->fetch();
            if ($ar) {
                $salt = (strlen($ar['PASSWORD']) > 32) ? substr($ar['PASSWORD'], 0, strlen($ar['PASSWORD']) - 32) : '';
                $this->access = ($salt . md5($salt . $_SERVER['HTTP_AUTHORIZATION_PASSWORD']) == $ar['PASSWORD']);
            }
        }
    }

    private function checkToken()
    {

        if ($this->parameter['USE_AUTH_TOKEN'] == 'Y') {

            $set_token = trim($this->parameter['RESTAPI_TOKEN']);
            $this->access = false;

            if ($set_token != '') {
                if (trim($_SERVER['HTTP_AUTHORIZATION_TOKEN']) == $set_token) {
                    $this->access = true;
                }
            }
        }
    }

    private function checkHttps()
    {
        if ($this->parameter['ONLY_HTTPS_EXCHANGE'] == 'Y' && $_SERVER['SERVER_PORT'] != 443) {
            $this->access = false;
        }
    }

    public function getRealIpAddr()
    {
        if (!$this->realIp) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $this->realIp = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $this->realIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $this->realIp = $_SERVER['REMOTE_ADDR'];
            }
        }
        return $this->realIp;
    }

    public function Requestget()
    {
        $ar = [
            'DATE' => date('Y-m-d H:i:s'),
            'REQUEST_METHOD' => self::getMethod(),
            'IP_ADDRESS' => self::getRealIpAddr(),
            'CONTROLLER' => self::getController(),
            'ACTION' => self::getAction(),
            'PARAMETERS'=>$this->getParameters(),
        ];
        if ($this->getApiVersion()) {
            $ar['API_VERSION'] = $this->getApiVersion();
        }
        if ($_SERVER['HTTP_AUTHORIZATION_TOKEN']) {
            $ar['AUTHORIZATION_TOKEN'] = $_SERVER['HTTP_AUTHORIZATION_TOKEN'];
        }
        if ($_SERVER['HTTP_AUTHORIZATION_LOGIN']) {
            $ar['AUTHORIZATION_LOGIN'] = $_SERVER['HTTP_AUTHORIZATION_LOGIN'];
        }
        if ($_SERVER['HTTP_AUTHORIZATION_PASSWORD']) {
            $ar['AUTHORIZATION_PASSWORD'] = $_SERVER['HTTP_AUTHORIZATION_PASSWORD'];
        }
        return $ar;
    }


    private function setHeadersSmall()
    {
        header('Powered: IT-Round 1999 -' . date('Y'));
        header('Support: https://it-round.ru');
        header('Content-Type: application/json; charset=utf-8');
    }

    public function ShowResult($data, $options = false)
    {
        $this->setHeadersSmall();
        header('HTTP/1.1 200');

        $result = json_encode(['status' => 200, 'result' => $data], $options);

        if ($error = $this->ckeckError()) {
            header('HTTP/1.1 500');
            $result = json_encode(['status' => 500, 'result' => $error]);
        }

        echo $result;
        die();
    }

    public function NoResult($message = '')
    {
        $this->setHeadersSmall();

        $message = ($message) ? $message : 'No Result';
        header('HTTP/1.1 200');
        echo json_encode(['status' => 200, 'error' => $message]);
        die();
    }

    public function BadRequest($message = '')
    {
        $this->setHeadersSmall();

        $message = ($message) ? $message : 'Bad Request';
        header('HTTP/1.1 400');
        echo json_encode(['status' => 400, 'error' => $message]);
        die();
    }

    public function DenyAccess()
    {
        $this->setHeadersSmall();
        header('HTTP/1.1 403');
        echo json_encode(['status' => 403, 'error' => 'Forbidden']);
        die();
    }

    private function ckeckError()
    {

        $result = false;

        switch (json_last_error()) {

            case JSON_ERROR_DEPTH:
                $result = 'JSON_ERROR_DEPTH';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $result = 'JSON_ERROR_STATE_MISMATCH';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $result = 'JSON_ERROR_CTRL_CHAR';
                break;
            case JSON_ERROR_SYNTAX:
                $result = 'JSON_ERROR_SYNTAX';
                break;
            case JSON_ERROR_UTF8:
                $result = 'JSON_ERROR_UTF8';
                break;
        }

        return $result;
    }
}