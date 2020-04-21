<?php

namespace ITRound\Vbchbbonus;

use CCatalogDiscount;
use CCatalogSku;
use ITRound\Vbchbbonus;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main,
    \Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Sale\Internals;
use \Bitrix\Main\Application;
use Bitrix\Main\Context;
use Vbcherepanov\Bonus\widget\OrderStatus;

Loc::loadMessages(__FILE__);

class Vbchbbcore {

   public $MODULE_EVENTS = array();
   public $MODULE_AGENTS = array();
   public $INSTALL_PROFILE = array();
   public $module_id = "vbcherepanov.bonus";
   public $mail_temp_prefix = "VBCH_BIGBONUS";
   public $mail_events_prefix = "MAIL";
   public $typeLocation = '/../type';
   public $DescriptionBonus = array();
   public $SITE_ID = '';
   public $PROFILES = array();
   public $AFFILIATE = array();
   public $UserBasket = array();
   public $UserOrder = array();
   public $arUSER = array();
   public $NEW_SALE_ORDER_AJAX = false;
   public $ACCOUNT_BITRIX_CLASS = '';
   public $FUNC_GETRULES = array();
   public $FUNC_GETBONUS = array();
   static public $OLDOFFERS = [];
   static public $FromPropBonusPay = [];

   public function __construct() {
      \CVbchbbEvents::includeModule();
      global $USER;
      if (!is_object($USER))
         $USER = new \CUser();
      $this->SITE_ID = self::GetSiteID();
      $path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->module_id;
      Loc::loadMessages($path . '/type.php');
      Loc::loadMessages($path . '/widget.php');
      $tmpAuth = $USER->IsAuthorized();
      $by = 'ID';
      $order = 'desc';
      $this->arUSER = array('AUTH' => $tmpAuth, ($tmpAuth ? $USER->GetList($by, $order,
                  array('ID' => $USER->GetID()), array('SELECT' => array('*', 'UF_*')))->Fetch() : array()),
          'GROUP' => $tmpAuth ? $this->GetUserGroupByUser($USER->GetID()) : array(2)
      );
      unset($tmpAuth);
      $prof = $this->includeProfiles();
      if ($this->CheckArray($prof)) {
         foreach ($prof as $profile) {
            if ($this->CheckArray($profile)) {
               $tmp = new Vbchbbonus\Vbchbbprofiles();
               $tmp->setCode($profile[0]);
               $tmp->setName($profile[1]);
               $tmp->setDesc($profile[2]);
               $tmp->setVisualParams($profile[3]);
               $this->INSTALL_PROFILE[$profile[0]] = $tmp;
               $this->MODULE_EVENTS[$profile[0]] = $profile[4];
               $this->MODULE_AGENTS[$profile[0]] = $profile[5];
               $this->FUNC_GETRULES[$profile[0]] = $profile['GetRules'];
               $this->FUNC_GETBONUS[$profile[0]] = $profile['GetBonus'];
               unset($tmp);
            }
         }
      }
      $this->SITE_ON = $this->CheckSerialize(Option::get($this->module_id, 'use_on_sites'));
      $CHECKSOA = $this->GetOptions($this->SITE_ID, "CHECKSOA");
      if ($CHECKSOA['OPTION'] == 'Y') {
         $filesoa = $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/components/bitrix/sale.order.ajax/class.php';
         if (file_exists($filesoa)) {
            \CBitrixComponent::includeComponentClass("bitrix:sale.order.ajax");
            if (class_exists('SaleOrderAjax'))
               $this->NEW_SALE_ORDER_AJAX = true;
         }
      } else {
         $this->NEW_SALE_ORDER_AJAX = true;
      }
   }

   public function GetCurrentProfiles() {
      $currentDatetime = new Main\Type\DateTime();
      $iterator = Vbchbbonus\CvbchbonusprofilesTable::getList(
                      [
                          'filter' => [
                              'ACTIVE' => "Y",
                              'SITE' => $this->SITE_ID,
                              [
                                  'LOGIC' => 'OR',
                                  'ACTIVE_FROM' => '',
                                  '<=ACTIVE_FROM' => $currentDatetime
                              ],
                              [
                                  'LOGIC' => 'OR',
                                  'ACTIVE_TO' => '',
                                  '>=ACTIVE_TO' => $currentDatetime
                              ]
                          ],
                          'cache' => [
                              'ttl' => 60,
                              'cache_joins' => true
                          ]
                      ]
      );
      $profiles = [];
      while ($profile = $iterator->fetch()) {
         $profiles[] = $profile;
      }
      unset($currentTime, $iterator, $profile);
      return $profiles;
   }

   public function GetCurrentAffiliate($filter = array()) {
      $currentDatetime = new Main\Type\DateTime();
      $filter['ACTIVE'] = 'Y';
      $filter['LID'] = $this->SITE_ID;
      $filter[] = [
          'LOGIC' => 'OR',
          'ACTIVE_FROM' => '',
          '<=ACTIVE_FROM' => $currentDatetime
      ];
      $filter[] = [
          'LOGIC' => 'OR',
          'ACTIVE_TO' => '',
          '>=ACTIVE_TO' => $currentDatetime
      ];

      $iterator = Vbchbbonus\CVbchAffiliateTable::getList(
                      [
                          'filter' => $filter,
                          'cache' => [
                              'ttl' => 60,
                              'cache_joins' => true
                          ]
                      ]
      );
      $affiliates = array();
      while ($affiliate = $iterator->fetch()) {
         $affiliates[] = $affiliate;
      }
      unset($currentTime, $iterator, $affiliate);
      return $affiliates;
   }

   public function GetBasket() {
      \Bitrix\Main\Loader::includeModule("sale");
      \Bitrix\Main\Loader::includeModule("catalog");
      \Bitrix\Main\Loader::includeModule("currency");
      $fUserId = \CSaleBasket::GetBasketUserID();
      $bs = Sale\Basket\Storage::getInstance($fUserId, $this->SITE_ID);
      $fullBasket = $bs->getBasket();
      $fuser = new \Bitrix\Sale\Discount\Context\Fuser($fullBasket->getFUserId(true));
      $orderableBasket = $bs->getOrderableBasket();
      $this->initializeBasketOrderIfNotExists($orderableBasket);
      $discounts = \Bitrix\Sale\Discount::buildFromBasket($fullBasket, $fuser);
      if ($discounts) {
         $discounts->calculate();
         $result = $discounts->getApplyResult(true);
         $prices = $result['PRICES']['BASKET'];
      } else
         $prices = [];
      foreach ($fullBasket as $item) {
         if ($item instanceof Sale\BasketItem && $item->canBuy() && !$item->isDelay()) {
            $item = $orderableBasket->getItemByBasketCode($item->getBasketCode());
            $basketItems[$item->getId()] = $this->processBasketItem($item);
            if (sizeof($prices) > 0) {
               foreach ($prices[$basketItems[$item->getId()]['ID']] as $key => $v) {
                  $basketItems[$item->getId()][$key] = $v;
               }
            }
         }
      }
      unset($discounts, $prices, $fUserId, $bs, $fuser, $fullBasket, $result, $orderableBasket, $item);


      return $this->CreateArFields($basketItems);
   }

   protected function initializeBasketOrderIfNotExists(Sale\Basket $basket) {
      global $USER;
      $uid = $USER instanceof CUser ? $USER->GetID() : null;
      if (!$basket->getOrder()) {
         $userId = $uid ?: \CSaleUser::GetAnonymousUserID();
         $order = Sale\Order::create($this->SITE_ID, $userId);

         $result = $order->appendBasket($basket);

         $discounts = $order->getDiscount();
         $showPrices = $discounts->getShowPrices();
         if (!empty($showPrices['BASKET'])) {
            foreach ($showPrices['BASKET'] as $basketCode => $data) {
               $basketItem = $basket->getItemByBasketCode($basketCode);
               if ($basketItem instanceof Sale\BasketItemBase) {
                  $basketItem->setFieldNoDemand('BASE_PRICE', $data['SHOW_BASE_PRICE']);
                  $basketItem->setFieldNoDemand('PRICE', $data['SHOW_PRICE']);
                  $basketItem->setFieldNoDemand('DISCOUNT_PRICE', $data['SHOW_DISCOUNT']);
               }
            }
         }
      }
   }

   function getBasketItemProperties(\Bitrix\Sale\BasketItem $basketItem) {
      $properties = array();
      /** @var Sale\BasketPropertiesCollection $propertyCollection */
      $propertyCollection = $basketItem->getPropertyCollection();
      $basketId = $basketItem->getBasketCode();

      foreach ($propertyCollection->getPropertyValues() as $property) {
         if ($property['CODE'] == 'CATALOG.XML_ID' || $property['CODE'] == 'PRODUCT.XML_ID' || $property['CODE'] == 'SUM_OF_CHARGE')
            continue;

         $property = array_filter($property, array('CSaleBasketHelper', 'filterFields'));
         $property['BASKET_ID'] = $basketId;
         $this->makeCompatibleArray($property);

         $properties[] = $property;
      }

      return $properties;
   }

   function processBasketItem(\Bitrix\Sale\BasketItem $item) {
      $basketItem = $item->getFieldValues();
      $weightKoef = htmlspecialcharsbx(Option::get('sale', 'weight_koef', 1, $this->SITE_ID));
      $weightUnit = htmlspecialcharsbx(Option::get('sale', 'weight_unit', "", $this->SITE_ID));
      $basketItem['PROPS'] = $this->getBasketItemProperties($item);
      $basketItem['PROPS_ALL'] = $item->getPropertyCollection()->getPropertyValues();
      $basketItem['QUANTITY'] = $item->getQuantity();

      $basketItem['WEIGHT'] = (float) $basketItem['WEIGHT'];
      $basketItem['WEIGHT_FORMATED'] = roundEx($basketItem['WEIGHT'] / $weightKoef, SALE_WEIGHT_PRECISION) . ' ' . $weightUnit;

      $basketItem['PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['PRICE']);
      $basketItem['CUSTOM_PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['CUSTOM_PRICE']);
      $basketItem['PRICE_FORMATED'] = \CCurrencyLang::CurrencyFormat($basketItem['PRICE'], $basketItem['CURRENCY'], true);

      $basketItem['FULL_PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['BASE_PRICE']);
      $basketItem['FULL_PRICE_FORMATED'] = \CCurrencyLang::CurrencyFormat($basketItem['FULL_PRICE'], $basketItem['CURRENCY'], true);

      $basketItem['DISCOUNT_PRICE'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['DISCOUNT_PRICE']);
      $basketItem['DISCOUNT_PRICE_FORMATED'] = \CCurrencyLang::CurrencyFormat($basketItem['DISCOUNT_PRICE'], $basketItem['CURRENCY'], true);

      $basketItem['SUM_VALUE'] = $basketItem['PRICE'] * $basketItem['QUANTITY'];
      $basketItem['SUM'] = \CCurrencyLang::CurrencyFormat($basketItem['SUM_VALUE'], $basketItem['CURRENCY'], true);

      $basketItem['SUM_FULL_PRICE'] = $basketItem['FULL_PRICE'] * $basketItem['QUANTITY'];
      $basketItem['SUM_FULL_PRICE_FORMATED'] = \CCurrencyLang::CurrencyFormat($basketItem['SUM_FULL_PRICE'], $basketItem['CURRENCY'], true);

      $basketItem['SUM_DISCOUNT_PRICE'] = $basketItem['DISCOUNT_PRICE'] * $basketItem['QUANTITY'];
      $basketItem['SUM_DISCOUNT_PRICE_FORMATED'] = \CCurrencyLang::CurrencyFormat($basketItem['SUM_DISCOUNT_PRICE'], $basketItem['CURRENCY'], true);

      $basketItem['PRICE_VAT_VALUE'] = $basketItem['VAT_VALUE'] = ($basketItem['PRICE'] * $basketItem['QUANTITY'] / ($basketItem['VAT_RATE'] + 1)) * $basketItem['VAT_RATE'] / $basketItem['QUANTITY'];

      if ($basketItem['CUSTOM_PRICE'] !== 'Y' && $basketItem['FULL_PRICE'] > 0 && $basketItem['DISCOUNT_PRICE'] > 0) {
         $basketItem['DISCOUNT_PRICE_PERCENT'] = \Bitrix\Sale\PriceMaths::roundPrecision($basketItem['DISCOUNT_PRICE'] * 100 / $basketItem['FULL_PRICE']);
      } else {
         $basketItem['DISCOUNT_PRICE_PERCENT'] = 0;
      }

      $basketItem['DISCOUNT_PRICE_PERCENT_FORMATED'] = \Bitrix\Sale\BasketItem::formatQuantity($basketItem['DISCOUNT_PRICE_PERCENT']) . '%';

      if ($basketItem['CAN_BUY'] !== 'Y' && $basketItem['DELAY'] !== 'Y') {
         $basketItem['NOT_AVAILABLE'] = true;
      }
      return $basketItem;
   }

   public function makeCompatibleArray(&$array) {
      if (empty($array) || !is_array($array))
         return;

      $arr = array();
      foreach ($array as $key => $value) {
         if (is_array($value) || preg_match("/[;&<>\"]/", $value)) {
            $arr[$key] = htmlspecialcharsEx($value);
         } else {
            $arr[$key] = $value;
         }

         $arr["~{$key}"] = $value;
      }

      $array = $arr;
   }

   public function CreateArFields($basket = array()) {
      $Element = array();
      $fields = array(
          "ID",
          'PRODUCT_ID',
          'PRODUCT_PRICE_ID',
          'QUANTITY',
          'CAN_BUY',
          'PRICE',
          "LID",
          "BASE_PRICE",
          'WEIGHT',
          'DETAIL_PAGE_URL',
          'CURRENCY',
          'VAT_RATE',
          'DISCOUNT_PRICE',
          'DISCOUNT_PRICE_PERCENT',
          'DISCOUNT_PRICE_PERCENT_FORMATED',
          'FULL_PRICE',
          "BASE_PRICE",
          "CUSTOM_PRICE",
          "DISCOUNT_NAME",
          "DISCOUNT_VALUE",
          "DISCOUNT_COUPON",
          "PRICE_TYPE_ID",
          "PRODUCT_PRICE_ID",
          "TYPE",
          "DISCOUNT_PRICE_PERCENT"
      );
      if (sizeof($basket) > 0) {
         foreach ($basket as $bskItem) {
            $p = array();
            foreach ($fields as $flds) {
               $p[$flds] = $bskItem[$flds];
            }
            $Element[$bskItem['PRODUCT_ID']] = $p;
         }
      }
      return array('ONE' => array_keys($Element), 'TWO' => $Element);
   }

   private function SelectFromIB($ID, $IBLOCK_ID = 0) {
      $element = [];
      if (\Bitrix\Main\Loader::includeModule("iblock")) {

         $select = [
             'ID',
             'NAME',
             'TIMESTAMP_X',
             'MODIFIED_BY',
             'DATE_CREATE',
             'CREATED_BY',
             'IBLOCK_ID',
             'IBLOCK_SECTION_ID',
             'ACTIVE',
             'ACTIVE_TO',
             'ACTIVE_FROM',
             'DATE_ACTIVE_FROM',
             'DATE_ACTIVE_TO',
             'SORT',
             'LOCK_STATUS',
             'IN_SECTIONS',
             'SHOW_COUNTER',
             'CODE',
             'TAGS',
             'XML_ID',
             'SECTION_ID',
             'EXTERNAL_ID',
             'USER_NAME',
             'CREATED_USER_NAME',
             'LID',
             'IBLOCK_TYPE_ID',
             'IBLOCK_CODE',
             'IBLOCK_NAME',
             'IBLOCK_EXTERNAL_ID',
             'DETAIL_PAGE_URL',
             'LIST_PAGE_URL',
             'CANONICAL_PAGE_URL',
             'CREATED_DATE',
             'PROPERTY_*',
         ];
         if (!is_array($ID))
            $ID = [$ID];
         $filter['ID'] = $ID;
         if ($IBLOCK_ID != 0)
            $filter['IBLOCK_ID'] = $IBLOCK_ID;
         $res = \CIBlockElement::GetList(
                         array(),
                         $filter,
                         false,
                         false,
                         $select);
         while ($b = $res->GetNextElement()) {
            foreach ($b->GetFields() as $fld => $vl) {
               if (in_array($fld, $select)) {
                  $element[$fld] = $vl;
               }
            }
            $element['PARENT_IBLOCK_ID'] = $element['IBLOCK_ID'];
            $element = array_filter($element);
            if (array_key_exists("IBLOCK_SECTION_ID", $element) && $element['IBLOCK_SECTION_ID']) {
               $ff = \CIBlockSection::GetNavChain($element['IBLOCK_ID'], $element['IBLOCK_SECTION_ID']);
               $sec = [];
               while ($g = $ff->Fetch()) {
                  $sec[] = $g['ID'];
               }
               $element['SECTION_ID'] = array_unique(array_merge([$element['IBLOCK_SECTION_ID']], $sec));
            }
            $element['PROPERTIES'] = $b->GetProperties();
         }
      }
      return $element;
   }

   public function GetPropAndDiscount($ElementID = array(), $Element = array()) {
      Loader::includeModule('sale');
      $offer1 = array();
      $disk_parent = [];
      if ($this->CheckArray($ElementID)) {
         foreach ($ElementID as $EL_ID) {
            $offer11 = $this->SelectFromIB($EL_ID);
            $disk_offer = $this->GetElemDiscount($offer11['ID'], $offer11['IBLOCK_ID']);



            if ($isSku = \CCatalogSKU::GetProductInfo($offer11['ID'], $offer11['IBLOCK_ID'])) {
               $parent = $this->SelectFromIB($isSku['ID'], $isSku['IBLOCK_ID']);
               $disk_parent = $this->GetElemDiscount($parent['ID'], $parent['IBLOCK_ID']);
            }
            if ($parent) {
               $offer11 = array_merge($offer11, $parent);
               $l = array_merge($offer11['PROPERTIES'], $parent['PROPERTIES']);
               unset($offer11['PROPERTIES'], $parent['PROPERTIES']);
            } else
               $l = $offer11['PROPERTIES'];
            $ll = $offer11;
            unset($offer11);
            $offer1[$EL_ID] = $ll;
            foreach ($this->CheckProperties($l) as $q => $iter) {
               $offer1[$EL_ID][$q] = $iter;
            }
            if ($disk_offer === $disk_parent) {
               $offer1[$EL_ID]['DISCOUNT'] = $disk_offer;
            } else
               $offer1[$EL_ID]['DISCOUNT'] = array_filter(array_merge($disk_offer, $disk_parent));
            $Element[$EL_ID]['CART_ID'] = $Element[$EL_ID]['ID'];
            unset($Element[$EL_ID]['ID']);
            $offer1[$EL_ID] = array_merge($offer1[$EL_ID], $Element[$EL_ID]);
            unset($l, $ll, $parent, $isSku, $offer11, $disk_offer, $disk_parent);
         }
      }
      return $offer1;
   }

   private function CheckProperties($properties) {
      $prop = [];
      //($props['MULTIPLE']=='Y') ? array($props['VALUE']) : $props['VALUE'];
      if (self::CheckArray($properties)) {
         foreach ($properties as $props) {
            if ($props['PROPERTY_TYPE'] == 'E' && $props['USER_TYPE'] == 'SKU') {
               $prop["PROPERTY_" . $props['ID'] . "_VALUE"] = (is_array($props['VALUE'])) ? $props['VALUE'] : [$props['VALUE']];
            }
            if ($props['PROPERTY_TYPE'] == 'S') {
               if ($props['USER_TYPE'] == 'directory') {
                  $prop["PROPERTY_" . $props['ID'] . "_VALUE"] = (is_array($props['VALUE'])) ? $props['VALUE'] : [$props['VALUE']]; //PROPERTY_VALUE_ID
               } elseif ($props['USER_TYPE'] == '') {
                  $prop["PROPERTY_" . $props['ID'] . "_VALUE"] = (is_array($props['VALUE'])) ? $props['VALUE'] : [$props['VALUE']];
               }
            } elseif ($props['PROPERTY_TYPE'] == 'L') {
               $prop["PROPERTY_" . $props['ID'] . "_VALUE"] = (is_array($props['VALUE_ENUM_ID'])) ? $props['VALUE_ENUM_ID'] : [$props['VALUE_ENUM_ID']]; //VALUE_ENUM || VALUE || VALUE_ENUM_ID
            } else {
               $prop["PROPERTY_" . $props['ID'] . "_VALUE"] = (is_array($props['VALUE'])) ? $props['VALUE'] : [$props['VALUE']]; //VALUE_ENUM || VALUE || VALUE_ENUM_ID
            }
         }
      }
      return array_filter($prop);
   }

   private function GetElemDiscount($Element_ID, $IBLOCK_ID) {
      $disk = [];
      $arMainCatalog = false;
      $arMainCatalog = CCatalogSku::GetInfoByIBlock($IBLOCK_ID);
      $arParams = array();
      if (CCatalogSku::TYPE_OFFERS == $arMainCatalog['CATALOG_TYPE']) {
         $arParams['SKU'] = 'Y';
         $arParams['SKU_PARAMS'] = array(
             'IBLOCK_ID' => $arMainCatalog['IBLOCK_ID'],
             'PRODUCT_IBLOCK_ID' => $arMainCatalog['PRODUCT_IBLOCK_ID'],
             'SKU_PROPERTY_ID' => $arMainCatalog['SKU_PROPERTY_ID'],
         );
      }
      $arParams['RENEWAL'] = 'N';
      $arParams['SITE_ID'] = $this->SITE_ID;

      $arDiscountList = CCatalogDiscount::GetDiscountForProduct(array('ID' => $Element_ID, 'IBLOCK_ID' => $IBLOCK_ID), $arParams);
      foreach ($arDiscountList as $discount) {
         $disk[] = array('ACTIVE' => true, 'ID' => $discount['ID']);
      }
      return $disk;
   }

   public function GetPayedBonusOrder($ORDER_ID) {
      $IDUNITS = "PAY_PART_" . $ORDER_ID;
      $CANCEL = "RETURN_PAY_PART_" . $ORDER_ID;
      $SelectBONUS = array();
      $return = true;
      foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $cls) {
         $res = call_user_func_array(array($cls, "getList"), array(array(
                 'filter' => array('ACTIVE' => 'Y', 'TYPES' => 'BONUSPAY'),
                 'cache' => array(
                     'ttl' => 60,
                     'cache_joins' => true,
                 )
         )));
         $result = 0;
         while ($bv = $res->fetch()) {
            $dopmor = $this->CheckSerialize($bv['OPTIONS']);
            if ($dopmor['IDUNITS'] == $IDUNITS) {
               $result = $bv['BONUS'];
            }
            if ($dopmor['IDUNITS'] == $CANCEL) {
               $result += $bv['BONUS'];
            }
         }
         $result = ($result > 0) ? $result * (-1) : $result;
      }
      return floatval($result);
   }

   public function GetSaleProfiles() {
      $l = $this->GetOptions($this->SITE_ID, "SALEPROFILETYPE");
      $l = $l['OPTION'];
      $l = ($l == '') ? array('ORDER', 'CART', 'FIRSTORDER', 'ORDERX') : explode(",", $l);
      return $l;
   }

   public function GetBonusFromOrderID($ORDER_ID) {
      $ORDER_ID = intval($ORDER_ID);
      if (empty($ORDER_ID))
         return 0;
      $bonus = 0;
      $arFields = $this->GetArrayForProfile(0, array(), 1, false, $ORDER_ID);
      $arFields['NONE'] = false;
      $profiles = $this->FilterProfiles($this->GetSaleProfiles());
      $bonusorderinprop = $this->GetOptions($this->SITE_ID, 'STARTBONUINORDERPROP');
      $bonusorderinprop = $bonusorderinprop['OPTION'] == 'Y';
      if ($bonusorderinprop) {
         $arOrder = \Bitrix\Sale\Order::load($ORDER_ID);
         $person_type = $arOrder->getField('PERSON_TYPE_ID');
         unset($arOrder);
      }
      $this->DescriptionBonus = array();
      if ($this->CheckArray($profiles)) {
         foreach ($profiles as $prof) {
            $Filter = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($this->FUNC_GETRULES[$prof['TYPE']], $prof['ID'], $this->CheckSerialize($prof['FILTER']), $arFields));
            if ($Filter) {
               $bonus = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields));
               $bonus = $this->BonusParams($bonus, $this->CheckSerialize($prof['BONUSCONFIG']));
               if ($bonusorderinprop) {
                  $SETTINGS = $this->CheckSerialize($prof['SETTINGS']);
                  if ($SETTINGS['ORDERPROPBONUS']['ACTIVE'] == 'Y') {
                     $p = $SETTINGS['ORDERPROPBONUS']['ID'];
                     $propID = $this->ReturnOrderProp($person_type, $p);
                     if ($propID) {
                        $this->AddOrderProperty($propID, $bonus['bonus'], $ORDER_ID);
                     }
                  }
               }
               $bns[$prof['ID']] = $bonus['bonus'];
               $this->DescriptionBonus[] = array($prof['ID'], $prof['NAME'], $this->ReturnCurrency($bonus['bonus']));
            }
         }
      }
      if ($this->CheckArray($bns)) {
         $bonus = $this->GetRangeBonus($bns);
      }
      return $bonus;
   }

   public function GetOfferBonusCart($data) {
      $result = [];
      foreach ($data as $key => &$item) {
         $MIN_PRICE = [
             'ID' => $item['PRODUCT_PRICE_ID'],
             'CAN_BUY' => "Y",
             'DISCOUNT_VALUE' => $item['DISCOUNT_VALUE'],
             'PRICE' => $item['PRICE'],
             'CURRENCY' => $item['CURRENCY'],
             'VATRATE_VALUE' => $item['VAT_RATE'],
             'PRICE_TYPE_ID' => $item['PRICE_TYPE_ID'],
             'PRICE_ID' => $item['PRODUCT_PRICE_ID'],
         ];
         $bns = $this->GetBonusElements($item['PRODUCT_ID'], $item['IBLOCK_ID'], $item['QUANTITY'], $MIN_PRICE);
         $item['BONUS'] = $this->ReturnCurrency($bns);
         unset($item);
      }
      return $data;
   }

   public function BonusPayFromCart($data) {
      $result = [];
      $result['ON'] = false;
      $result['MAXPAY'] = 0;
      $result['USER_INPUT'] = false;
      foreach ($data as $key => &$item) {
         $MIN_PRICE = [
             'ID' => $item['PRODUCT_PRICE_ID'],
             'CAN_BUY' => "Y",
             'DISCOUNT_VALUE' => $item['DISCOUNT_VALUE'],
             'PRICE' => $item['PRICE'],
             'CURRENCY' => $item['CURRENCY'],
             'VATRATE_VALUE' => $item['VAT_RATE'],
             'PRICE_TYPE_ID' => $item['PRICE_TYPE_ID'],
             'PRICE_ID' => $item['PRODUCT_PRICE_ID'],
         ];
         unset($item);
      }


      return $result;
   }

   public function GetCartOrderBonus($type, $arParams = array()) {
      $bonus = 0;
      $bns = array();
      if ($this->CheckArray($arParams)) {
         foreach ($arParams as $key => $val) {
            $arFields[$key] = $val;
         }
      } else {
         $arFields = $this->GetArrayForProfile(0, array(), 1, true, 0);
      }
      $arFields['NONE'] = false;
      $profiles = $this->FilterProfiles($this->GetSaleProfiles());
      $this->DescriptionBonus = array();
      if ($this->CheckArray($profiles)) {
         foreach ($profiles as $prof) {
            $Filter = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($this->FUNC_GETRULES[$prof['TYPE']], $prof['ID'], $this->CheckSerialize($prof['FILTER']), $arFields));
            if ($Filter) {
               $bonus = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields));
               $bonus = $this->BonusParams($bonus, $this->CheckSerialize($prof['BONUSCONFIG']));
               $bns[$prof['ID']] = $bonus['bonus'];
               $this->DescriptionBonus[] = array($prof['ID'], $prof['NAME'], $this->ReturnCurrency($bonus['bonus']));
            }
         }
      }
      if ($this->CheckArray($bns)) {
         $bonus = $this->GetRangeBonus($bns);
      }
      return $bonus;
   }

   public function GetBonusOfferList(&$arResultList, $type = 'ITEMS') {
      if ($this->CheckArray($arResultList[$type])) {
         foreach ($arResultList[$type] as &$arItem) {
            if (!$this->CheckArray($arItem['MIN_PRICE'])) {
               $arItem['MIN_PRICE'] = $arItem['OFFERS'][$arItem['OFFERS_SELECTED']]['MIN_PRICE'];
            }
            if (!is_array($arItem['MIN_PRICE']) && array_key_exists('ITEM_PRICES', $arItem)) {
               $arItem['MIN_PRICE'] = [
                   'ID' => $arItem['ITEM_PRICES'][0]['ID'],
                   'CAN_BUY' => 'Y',
                   'PRICE_ID' => $arItem['ITEM_PRICES'][0]['PRICE_TYPE_ID'],
                   'CURRENCY' => $arItem['ITEM_PRICES'][0]['CURRENCY'],
                   'DISCOUNT_VALUE' => $arItem['ITEM_PRICES'][0]['PRICE'],
                   'VALUE' => $arItem['ITEM_PRICES'][0]['PRICE'],
               ];
            }
            $b = $this->GetBonusElements($arItem['ID'], $arItem['IBLOCK_ID'], 1, $arItem['MIN_PRICE'], array());
            $bonuses = [
                'BONUS' => $b,
                'BONUS_FORMATED' => $this->ReturnCurrency($b),
                'PERCENT' => \Bitrix\Sale\Discount::calculateDiscountPercent($arItem['MIN_PRICE']['VALUE'], $b),
            ];
            $arItem['BONUSEL'] = $bonuses;
            unset($arItem);
         }
      }
   }

   public function GetBonusElements($ID, $IBLOCK_ID, $COUNT = 1, $arPRICE = array(), $arResult = array()) {
      $arFields = $this->GetArrayForProfile($ID, $arPRICE, $COUNT);
      $arFields['NONE'] = false;
      $bns = array();
      $bonus = 0;
      $profiles = $this->FilterProfiles($this->GetSaleProfiles());
      if ($this->CheckArray($profiles)) {
         $this->DescriptionBonus = array();
         foreach ($profiles as $prof) {
            $Filter = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($this->FUNC_GETRULES[$prof['TYPE']], $prof['ID'], $this->CheckSerialize($prof['FILTER']), $arFields));
            if ($Filter) {
               $bonus = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields));
               $bonus = $this->BonusParams($bonus, $this->CheckSerialize($prof['BONUSCONFIG']));
               $bns[$prof['ID']] = $bonus['bonus'];
               $this->DescriptionBonus[] = array($prof['ID'], $prof['NAME'], $this->ReturnCurrency($bonus['bonus']));
            }
         }
      }
      if ($this->CheckArray($bns)) {
         $bonus = $this->GetRangeBonus($bns);
      }
      return $bonus;
   }

   function GetArrayForProfile($ElementID = 0, $MIN_PRICE = array(), $count = 1, $basket = false, $ORDER_ID = 0) {
      \Bitrix\Main\Loader::includeModule("sale");
      \Bitrix\Main\Loader::includeModule("catalog");
      $allOFFER = array();
      global $USER;
      if (empty($MIN_PRICE)) {
         $min_price = \CCatalogProduct::GetOptimalPrice($ElementID, $count, $USER->GetUserGroupArray(), 'N', array(), $this->SITE_ID, array());
         $MIN_PRICE = [
             'ID' => $min_price['PRICE']['ID'],
             'CAN_BUY' => "Y",
             'DISCOUNT_VALUE' => $min_price['RESULT_PRICE']['DISCOUNT_PRICE'],
             'PRICE' => $min_price['RESULT_PRICE']['DISCOUNT_PRICE'],
             'CURRENCY' => $min_price['RESULT_PRICE']['CURRENCY'],
             'VATRATE_VALUE' => $min_price['RESULT_PRICE']['VAT_RATE'],
             'DISCOUNT_DIFF' => $min_price['RESULT_PRICE']['DISCOUNT'],
             'PRICE_TYPE_ID' => $min_price['RESULT_PRICE']['PRICE_TYPE_ID'],
             'PRICE_ID' => $min_price['RESULT_PRICE']['PRICE_TYPE_ID'],
         ];
      }
      //get basket
      $withBasket = $this->GetOptions($this->SITE_ID, 'WITHBASKET');
      $withBasket = ($withBasket['OPTION'] == 'Y');
      if ($basket) {
         if ($this->checkArray($this->UserBasket))
            $allOFFER = $this->UserBasket;
         else {
            $tmp = $this->GetBasket();
            $allOFFER = $this->GetPropAndDiscount($tmp['ONE'], $tmp['TWO']);
         }
      }
      // get offer (detail page) - send two parameters ID and MIN_PRICE (array minimal price)
      if ($ElementID != 0 && sizeof($MIN_PRICE) > 0) {
         if ($count == 0)
            $count = 1;

         $offer_Cat[$ElementID] = array(
             'PRODUCT_ID' => $ElementID,
             'PRODUCT_PRICE_ID' => $MIN_PRICE['ID'],
             'QUANTITY' => $count,
             'CAN_BUY' => $MIN_PRICE['CAN_BUY'],
             'PRICE' => $MIN_PRICE['DISCOUNT_VALUE'] ? $MIN_PRICE['DISCOUNT_VALUE'] : $MIN_PRICE['PRICE'],
             //'WEIGHT'=>$MIN_PRICE['PRODUCT_WEIGHT'],
             //'DETAIL_PAGE_URL'=>'',
             'CURRENCY' => $MIN_PRICE['CURRENCY'],
             'VAT_RATE' => $MIN_PRICE['VATRATE_VALUE'],
             'DISCOUNT_PRICE' => $MIN_PRICE['DISCOUNT_DIFF'],
             'DISCOUNT_PRICE_PERCENT' => $MIN_PRICE['DISCOUNT_DIFF_PERCENT'],
             'DISCOUNT_PRICE_PERCENT_FORMATED' => $MIN_PRICE['DISCOUNT_DIFF_PERCENT'] . '%',
             'FULL_PRICE' => $MIN_PRICE['DISCOUNT_VALUE'] + $MIN_PRICE['DISCOUNT_DIFF'],
             'CATALOG_GROUP_ID' => $MIN_PRICE['PRICE_ID'] ? $MIN_PRICE['PRICE_ID'] : $MIN_PRICE['PRICE_TYPE_ID'],
         );
         $offer = $this->GetPropAndDiscount(array($ElementID), $offer_Cat);
         if ($basket && $withBasket)
            $allOFFER = array_merge($offer, $allOFFER);
         else
            $allOFFER = $offer;
      }
      // get order parameters (delivery, payment...)
      $arBasketItem = array();
      if ($ORDER_ID) {
         $order = \Bitrix\Sale\Order::load($ORDER_ID);
         $basket = $order->getBasket();
         foreach ($basket->getBasketItems() as $bsk) {
            $arBasketItem[] = $bsk->getFieldValues();
         }
         $tmp = $this->CreateArFields($arBasketItem);

         if (sizeof($allOFFER) > 0)
            $ORDER['BASKET'] = array_merge($this->GetPropAndDiscount($tmp['ONE'], $tmp['TWO']), $allOFFER);
         else
            $ORDER['BASKET'] = $this->GetPropAndDiscount($tmp['ONE'], $tmp['TWO']);
         $discount_order_list = $order->getDiscount()->getApplyResult();
         $ORDER['DISCOUNT_ORDER_LIST'] = $discount_order_list['FULL_DISCOUNT_LIST'];
         if (!$ORDER['DISCOUNT_ORDER_LIST'] && $discount_order_list['DISCOUNT_LIST']) {
            $ORDER['DISCOUNT_ORDER_LIST'] = $discount_order_list['DISCOUNT_LIST'];
         }
         $ORDER['ORDER_ID'] = $ORDER_ID;
         $ORDER['LID'] = $order->getField('LID');
         $ORDER['ORDER_PRICE'] = $order->getField('PRICE') - $order->getField('PRICE_DELIVERY');
         $ORDER['DELIVERY_PRICE'] = $order->getField('PRICE_DELIVERY');
         $ORDER['DELIVERY_TYPE'] = $order->getField('DELIVERY_ID');
         $ORDER['PAYMENT_TYPE'] = $order->getField('PAY_SYSTEM_ID');
         $ORDER['PERSON_TYPE_ID'] = $order->getField('PERSON_TYPE_ID');
         $ORDER['CANCEL'] = $order->getField('CANCEL');
         $ORDER['PAYED'] = $order->getField('PAYED');
         $ORDER['SUM_PAID'] = $order->getField('SUM_PAID');
         $ORDER['STATUS'] = $order->getField('STATUS_ID');
         $ORDER['USERGROUP'] = $this->GetUserGroupByUser($order->getField('USER_ID'));
         $ORDER['USERID'] = $order->getField('USER_ID');
         $delivery_id = array();
         $pay_sys = array();
         $del_price = 0;
         $shipments = $order->getShipmentCollection();
         foreach ($shipments as $shipment) {
            if (!$shipment->isSystem()) {
               $flds = $shipment->getFieldValues();
               $del_price += $flds['PRICE_DELIVERY'];
               $delivery_id[] = $flds['DELIVERY_ID'];
            }
         }
         $ORDER['DELIVERY_PRICE'] = $del_price;
         $ORDER['DELIVERY_ID'] = array_unique($delivery_id);
         $payments = $order->getPaymentCollection();
         foreach ($payments as $payment) {
            $flds = $payment->getFieldValues();
            $pay_sys[] = $flds['PAY_SYSTEM_ID'];
         }
         $ORDER['PAY_SYSTEM_ID'] = array_unique($pay_sys);
         $ORDER['USERORDERCOUNT'] = $this->GetOrderCountByUser($ORDER['USERID'], $ORDER['ORDER_ID']);
      } else {
         $sum = 0;
         foreach ($allOFFER as $p) {
            $sum += $p['PRICE'] * $p['QUANTITY'];
         }
         $ORDER = array(
             'ORDER_ID' => $ORDER_ID,
             'LID' => $this->SITE_ID,
             'ORDER_PRICE' => $sum,
             'DELIVERY_PRICE' => '',
             'DELIVERY_TYPE' => '',
             'DELIVERY_ID' => array(),
             'BASKET' => $allOFFER,
             'PAYMENT_TYPE' => '',
             'PAY_SYSTEM_ID' => array(),
             'PERSON_TYPE_ID' => '',
             'CANCEL' => '',
             'PAYED' => '',
             'SUM_PAID' => '',
             'STATUS' => '',
             'USERGROUP' => $this->arUSER['GROUP'],
             'USERID' => $this->arUSER[0]['ID'],
             'USERORDERCOUNT' => $this->GetOrderCountByUser($this->arUSER[0]['ID'], $ORDER_ID)
         );
      }
      return $ORDER;
   }

   public function GetUserBonus($USER_ID, $BONUS_ACCOUNT_TYPE, $accountID = null) {
      $count = 0;
      if ($USER_ID) {
         if ($BONUS_ACCOUNT_TYPE == 'BONUSPAY') {
            $dbAccountUser = Vbchbbonus\AccountTable::getList(array(
                        'filter' => array("USER_ID" => $USER_ID, 'BONUSACCOUNTSID' => $accountID),
                        'cache' => array(
                            'ttl' => 60,
                            'cache_joins' => true,
                        )
                    ))->fetch();
            if ($this->CheckArray($dbAccountUser)) {
               $count = $dbAccountUser["CURRENT_BUDGET"];
            }
         } elseif ($BONUS_ACCOUNT_TYPE == 'SYSTEMPAY') {
            $dbAccountList = \CSaleUserAccount::GetList(
                            array("CURRENCY" => "ASC"),
                            array("USER_ID" => IntVal($USER_ID)),
                            false,
                            false,
                            array("ID", "CURRENT_BUDGET")
            );
            if ($arAccountList = $dbAccountList->GetNext()) {
               $count = $arAccountList["CURRENT_BUDGET"];
            }
         }
         return $count;
      }
   }

   public function FilterProfiles($fltr) {
      $result = array();
      $l = $this->GetCurrentProfiles();
      if ($this->CheckArray($l)) {
         $filter = new Filter($l);
         $result = $filter
                 ->byField('TYPE', $fltr)
                 ->getResult();
      }
      return $result;
   }

   public function CheckSiteOn() {
      return ($this->SITE_ON[$this->SITE_ID] == 'Y');
   }

   public function GetTypes() {
      $profileTypes = array();
      foreach ($this->INSTALL_PROFILE as $file) {
         $profileTypes[] = call_user_func_array(array($file, "GetMenuAdd"), array());
         unset($clTypes);
      }
      return $profileTypes;
   }

   public function BonusBirthday() {
      @set_time_limit(0);
      ignore_user_abort(true);
      global $USER;
      if (!is_object($USER))
         $USER = new \CUser();
      $ss = $this->GetSiteSID();
      $res = $this->FilterProfiles('BITHDAY');
      if ($this->CheckArray($res)) {
         foreach ($res as $r) {
            $l[$r['SITE']][] = $r;
         }
      }
      foreach ($ss as $sid) {
         $this->SITE_ID = $sid;
         if ($this->CheckSiteOn()) {
            $res = $l[$sid];
            if ($this->CheckArray($res)) {
               foreach ($res as $prof) {
                  $filter = $this->CheckSerialize($prof['FILTER']);
                  if (in_array(2, $filter['USERGROUP'])) {
                     $fltr = array();
                  } else
                     $fltr = array('GROUP_ID' => $filter['USERGROUP']);
                  $res = \Bitrix\Main\UserGroupTable::getList(
                                  array(
                                      'filter' => $fltr,
                                      'cache' => array(
                                          'ttl' => 60,
                                          'cache_joins' => true,
                                      )
                                  )
                  );
                  $usergroup = array();
                  while ($row = $res->fetch()) {
                     $usergroup[] = $row['USER_ID'];
                  }
                  $usergroup = array_unique($usergroup);

                  $filt = array('ACTIVE' => $filter['ACTIVATE'], 'ID' => $usergroup);
                  $filt[$filter['BIRTHDAYFIELDS']] = '_%';
                  $edc = \CUser::GetList($by, $order, $filt, array('SELECT' => array("*", "UF_*")));
                  while ($q = $edc->Fetch()) {
                     if ($q[$filter['BIRTHDAYFIELDS']] != '')
                        $rsUsers[] = $q;
                  }
                  if (\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isAdminSection()) {
                     $check = ($prof['ISADMIN'] == 'Y');
                  } else
                     $check = true;
                  if ($check) {
                     if ($this->CheckArray($rsUsers)) {
                        foreach ($rsUsers as $usr) {
                           $sett = $this->CheckSerialize($prof['SETTINGS']);
                           $Filter = call_user_func_array(array($this->INSTALL_PROFILE['BITHDAY'], "GetRules"), array($this->FUNC_GETRULES['BITHDAY'], $prof['ID'], $this->CheckSerialize($prof['FILTER']), array($usr, $sett)));
                           if ($Filter) {
                              $bonus = call_user_func_array(array($this->INSTALL_PROFILE['BITHDAY'], "GetBonus"), array($this->FUNC_GETBONUS['BITHDAY'], $prof, array()));
                              $bonus = $this->BonusParams($bonus, $this->CheckSerialize($prof['BONUSCONFIG']));
                              $bonus['bonus'] = $this->GetAllBonus($bonus['bonus'], $bonus['bonus'], true);
                              $this->AddBonus($bonus, array('IDUNITS' => 'BITHDAY_' . $usr['ID'] . '_' . date("Y"), 'ORDER_ID' => '', 'USER_ID' => $usr['ID'], 'SITE_ID' => $this->SITE_ID), $prof);
                           }
                        }
                     }
                  }
               }
            }
         }
      }
   }

   public function BonusStatistic() {
      @set_time_limit(0);
      ignore_user_abort(true);
      global $USER;
      if (!is_object($USER))
         $USER = new \CUser();
      $ss = $this->GetSiteSID();
      \Bitrix\Main\Loader::includeModule("sale");
      $FIELDS = array();
      foreach ($ss as $sid) {
         $this->SITE_ID = $sid;
         if ($this->CheckSiteOn()) {
            $accountBon = \CSaleUserAccount::GetList(array(), array(">CURRENT_BUDGET" => 0), false, false, array());
            while ($accBon = $accountBon->GetNext()) {
               if ($accBon['CURRENCY'] == $this->ReturnCurrency(1)) {
                  $FIELDS[] = array(
                      'LID' => $sid,
                      'BONUS' => "",
                      'ACTIVE' => 'N',
                      'ACTIVE_FROM' => "",
                      'ACTIVE_TO' => "",
                      'TYPES' => "",
                      'USERID' => $accBon['USER_ID'],
                      'DESCRIPTION' => "",
                      'MAIL_TYPE' => "STATISTIC",
                      'BONUS_ALL' => $this->ReturnCurrency($accBon['CURRENT_BUDGET'])
                  );
               }
            }
            $accountBon = Vbchbbonus\AccountTable::getlist(array(
                        'filter' => array(">CURRENT_BUDGET" => 0),
                        'cache' => array(
                            'ttl' => 60,
                            'cache_joins' => true,
                        )
            ));
            while ($accBon = $accountBon->fetch()) {
               if ($accBon['CURRENCY'] == $this->ReturnCurrency(1)) {
                  $FIELDS[] = array(
                      'LID' => $sid,
                      'BONUS' => "",
                      'ACTIVE' => 'N',
                      'ACTIVE_FROM' => "",
                      'ACTIVE_TO' => "",
                      'TYPES' => "",
                      'USERID' => $accBon['USER_ID'],
                      'DESCRIPTION' => "",
                      'MAIL_TYPE' => "STATISTIC",
                      'BONUS_ALL' => $this->ReturnCurrency($accBon['CURRENT_BUDGET'])
                  );
               }
            }
            if ($this->CheckArray($FIELDS)) {
               $sendSMS = $this->GetOptions($this->SITE_ID, "MONTHSTATSMS");
               $smsTemplate = $this->GetOptions($this->SITE_ID, "MONTHSTATSMSTEMPLATE");
               foreach ($FIELDS as $fls) {
                  $this->Notification($fls, array("SENDSMS" => $sendSMS['OPTION'], "SENDEMAIL" => "Y", "SENDADMIN" => "N", "EMAILTEMPLATE" => $smsTemplate['OPTION']));
               }
            }
         }
      }
   }

   public function BonusLive() {
      @set_time_limit(0);
      ignore_user_abort(true);
      global $USER;
      if (!is_object($USER))
         $USER = new \CUser();
      $ss = $this->GetSiteSID();
      foreach ($ss as $sid) {
         $this->SITE_ID = $sid;
         if ($this->SITE_ON) {
            $option = $this->GetOptions($sid, 'DATEFORMAT');
            $option1 = $this->GetOptions($sid, 'AFTERDAY');
            $option1 = $option1['OPTION'];
            $this->SITE_ID = $sid;
            if ($this->CheckSiteOn()) {
               $userID = 0;
               $currentbudget = 0;
               $days = new \Bitrix\Main\Type\DateTime();
               if ($option1 == '' && $option1 <= 0)
                  $option1 = 1;
               $days->add("+" . $option1 . ' days');
               $daysFrom = clone $days;
               $daysFrom->setTime(0, 0, 0);
               $daysTo = clone $days;
               $daysTo->setTime(23, 59, 59);
               foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $id => $cls) {
                  if ($id == 0)
                     $tt = 'SYSTEMPAY';
                  else
                     $tt = 'BONUSPAY';
                  $res1 = call_user_func_array(array($cls, "getList"), array(array(
                          'filter' => array('ACTIVE' => 'Y', 'LID' => $sid, '>BONUS' => 0, array(
                                  'LOGIC' => 'AND',
                                  array(
                                      '>=ACTIVE_TO' => $daysFrom,
                                      '<=ACTIVE_TO' => $daysTo
                                  )
                              )))
                  ));
                  if ($res1) {
                     while ($rs = $res1->fetch()) {
                        $evntID = $this->GetOptions($sid, $this->mail_events_prefix . "_PREDELETE");
                        $evntID = $evntID['OPTION'];
                        $event = new \CEvent();
                        $usrs = $this->GetUserInfo($rs['USERID']);
                        $usr_bns = $this->GetUserBonus($rs['USERID'], $tt, $rs['BONUSACCOUNTSID']);
                        $flds = array(
                            'USEREMAIL' => $usrs['EMAIL'],
                            'BONUS' => $rs['BONUS'] - $rs['PARTPAY'],
                            'DAY' => $option1,
                            'USERNAME' => $usrs['FIO'],
                            'ACTIVE_FROM' => $rs['ACTIVE_FROM'],
                            'ACTIVE_TO' => $rs['ACTIVE_TO'],
                            'BONUS_ALL' => $usr_bns - $rs['BONUS'],
                        );
                        $ll = $event->Send($this->mail_temp_prefix, $sid, $flds, "N", $evntID);
                     }
                  }
                  $res = call_user_func_array(array($cls, "getList"), array(array(
                          'filter' => array('ACTIVE' => 'Y', 'LID' => $sid, '>BONUS' => 0, '<=ACTIVE_TO' => new \Bitrix\Main\Type\DateTime()
                          ))
                  ));
                  while ($bon = $res->fetch()) {
                     $Fields = $bon;
                     unset($Fields['ID']);
                     $Fields['ACTIVE'] = 'N';
                     $Fields['ACTIVE_FROM'] = $Fields['ACTIVE_TO'] = '';
                     $userID = $bon['USERID'];
                     $bonus = $bon['BONUS'] - $bon['PARTPAY'];
                     $BONUSACCOUNTSID = $bon['BONUSACCOUNTSID'];
                     call_user_func_array(array($cls, "update"), array($bon['ID'], $Fields));
                     if ($cls == 'ITRound\\Vbchbbonus\\TmpTable') {
                        $arOldUserAccount = \CSaleUserAccount::GetByID($userID);
                        $USER_ID = $arOldUserAccount["USER_ID"];
                        $CURRENCY = $arOldUserAccount["CURRENCY"];
                        $OLD_BUDGET = DoubleVal($arOldUserAccount["CURRENT_BUDGET"]);
                        $updateSum = $OLD_BUDGET - $bonus;
                        $currentbudget = $updateSum;
                        \CSaleUserAccount::UpdateAccount($USER_ID, $updateSum, $CURRENCY, "MANUAL");
                     } else {
                        $BonusAccount = Vbchbbonus\AccountTable::getList(array(
                                    'filter' => array('USER_ID' => $userID, 'BONUSACCOUNTSID' => $BONUSACCOUNTSID),
                                    'cache' => array(
                                        'ttl' => 60,
                                        'cache_joins' => true,
                                    )
                        ));
                        if ($bonAc = $BonusAccount->fetch()) {

                           $currentbudget = floatval($bonAc['CURRENT_BUDGET'] - $bonus);
                           $newFlds = $bonAc;
                           unset($newFlds['ID']);
                           $newFlds['CURRENT_BUDGET'] = $currentbudget;
                           $newFlds['BONUSACCOUNTSID'] = $BONUSACCOUNTSID;
                           Vbchbbonus\AccountTable::update($bonAc['ID'], $newFlds);
                        }
                     }
                     $l = $this->CheckSerialize($bon['OPTIONS']);
                     $profile = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                                 'filter' => array('ACTIVE' => 'Y', 'ID' => $l['PROFILE'], 'SITE' => $this->SITE_ID),
                                 'cache' => array(
                                     'ttl' => 60,
                                     'cache_joins' => true,
                                 )
                             ))->fetch();
                     $FIELDS = array(
                         'LID' => $bon['LID'],
                         'BONUS' => $bon['BONUS'] - $bon['PARTPAY'],
                         "DAY" => 0,
                         'ACTIVE' => 'N',
                         'ACTIVE_FROM' => $bon['ACTIVE_FROM'],
                         'ACTIVE_TO' => $bon['ACTIVE_TO'],
                         'TYPES' => $bon['TYPE'],
                         'USERID' => $bon['USERID'],
                         'DESCRIPTION' => $bon['DESCRIPTION'],
                     );
                     $option1 = $this->GetOptions($sid, 'BONUSNAME');
                     $FIELDS['MAIL_TYPE'] = ((-$bon['BONUS']) < 0) ? 'DELETE' : 'ADD';
                     $FIELDS['BONUS'] = $this->ReturnCurrency($FIELDS['BONUS']);
                     $FIELDS['BONUS_ALL'] = $this->ReturnCurrency($currentbudget);
                     $this->Notification($FIELDS, $this->CheckSerialize($profile['NOTIFICATION']));
                  }
               }
            }
         }
      }
   }

   public function BonusActive() {
      @set_time_limit(0);
      ignore_user_abort(true);
      global $USER;
      if (!is_object($USER))
         $USER = new \CUser();
      $ss = $this->GetSiteSID();
      foreach ($ss as $sid) {
         $option = $this->GetOptions($sid, 'DATEFORMAT');
         $this->SITE_ID = $sid;
         if ($this->CheckSiteOn()) {
            $currentbudget = 0;
            foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $cls) {
               $res = call_user_func_array(array($cls, "getList"), array(array(
                       'filter' => array('ACTIVE' => 'N', '=PARTPAY' => 0, '<=ACTIVE_FROM' => new \Bitrix\Main\Type\DateTime())),
               ));
               if ($res) {
                  while ($bon = $res->fetch()) {
                     $newFields = $bon;
                     unset($newFields['ID']);
                     $newFields['ACTIVE'] = 'Y';
                     $userID = $bon['USERID'];
                     $bonus = $bon['BONUS'];
                     $BONUSACCOUNTSID = $bon['BONUSACCOUNTSID'];
                     call_user_func_array(array($cls, "update"), array($bon['ID'], $newFields));
                     $BonusAccount = Vbchbbonus\AccountTable::getList(array(
                                 'filter' => array('USER_ID' => $userID, 'BONUSACCOUNTSID' => $BONUSACCOUNTSID),
                                 'cache' => array(
                                     'ttl' => 60,
                                     'cache_joins' => true,
                                 )
                     ));
                     if ($bonAc = $BonusAccount->fetch()) {
                        $currentbudget = floatval($bonAc['CURRENT_BUDGET'] + $bonus);
                        $newFlds = $bonAc;
                        unset($newFlds['ID']);
                        $newFlds['CURRENT_BUDGET'] = $currentbudget;
                        $newFlds['BONUSACCOUNTSID'] = $BONUSACCOUNTSID;
                        Vbchbbonus\AccountTable::update($bonAc['ID'], $newFlds);
                     } else {
                        if ($cls == 'ITRound\\Vbchbbonus\\TmpTable') {
                           $cur = $this->ReturnCurrency($bonus);
                           \CSaleUserAccount::UpdateAccount($userID, $bonus, $cur, "");
                        } else {
                           $ACC_FIELDS = array(
                               'USER_ID' => $userID,
                               'CURRENT_BUDGET' => $bonus,
                               'CURRENCY' => $this->ReturnCurrency($bonus),
                               'NOTES' => '',
                               'BONUSACCOUNTSID' => $BONUSACCOUNTSID,
                           );
                           Vbchbbonus\AccountTable::add($ACC_FIELDS);
                        }
                     }
                     $l = $this->CheckSerialize($bon['OPTIONS']);
                     $profile = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                                 'filter' => array('ACTIVE' => 'Y', 'ID' => $l['PROFILE'], 'SITE' => $this->SITE_ID),
                                 'cache' => array(
                                     'ttl' => 60,
                                     'cache_joins' => true,
                                 )
                             ))->fetch();
                     $FIELDS = array(
                         'LID' => $bon['LID'],
                         'BONUS' => $bon['BONUS'],
                         'ACTIVE' => 'Y',
                         'ACTIVE_FROM' => $bon['ACTIVE_FROM'],
                         'ACTIVE_TO' => $bon['ACTIVE_TO'],
                         'TYPES' => $bon['TYPE'],
                         'USERID' => $userID,
                         'DESCRIPTION' => $bon['DESCRIPTION'],
                     );
                     $FIELDS['MAIL_TYPE'] = ($bon['BONUS'] < 0) ? 'DELETE' : 'ADD';
                     $FIELDS['BONUS'] = $this->ReturnCurrency($FIELDS['BONUS']);
                     $FIELDS['BONUS_ALL'] = $this->ReturnCurrency($currentbudget);
                     $this->Notification($FIELDS, $this->CheckSerialize($profile['NOTIFICATION']));
                  }
               }
            }
         }
      }
   }

   public function GetAction() {
      if (Loader::includeModule("sale") && Loader::includeModule("catalog")) {
         $dbResultList = Internals\DiscountTable::getList(array(
                     'filter' => array('ACTIVE' => 'Y'),
                     'select' => array("*"),
                     'cache' => array(
                         'ttl' => 60,
                         'cache_joins' => true,
                     )
         ));
         while ($dsc = $dbResultList->fetch()) {
            $discount[] = array("ID" => $dsc['ID'], 'NAME' => $dsc['NAME'] . ' - ' . ($dsc['LID'] ? $dsc['LID'] : $dsc['SITE_ID']));
         }
      } else
         $discount = false;
      return $discount;
   }

   public function GetDiscount() {
      $discount = array();
      $dbResultList = array();
      if (Loader::includeModule("sale") && Loader::includeModule("catalog")) {
         if (\Bitrix\Main\Config\Option::get('sale', 'use_sale_discount_only') == 'Y') {
            $dbResultList = Internals\DiscountTable::getList(array(
                        'filter' => array('LID' => $this->SITE_ID),
                        'select' => array("*"),
                        'cache' => array(
                            'ttl' => 60,
                            'cache_joins' => true,
                        )
            ));
         } else {
            $dbResultList = \Bitrix\Catalog\DiscountTable::getList(array(
                        'filter' => array('SITE_ID' => $this->SITE_ID),
                        'select' => array("*"),
                        'cache' => array(
                            'ttl' => 60,
                            'cache_joins' => true,
                        )
            ));
         }
         while ($dsc = $dbResultList->fetch()) {
            $strDiscountValue = '';
            $type = $dsc['SHORT_DESCRIPTION_STRUCTURE']["VALUE_TYPE"] ? $dsc['SHORT_DESCRIPTION_STRUCTURE']["VALUE_TYPE"] : $dsc['VALUE_TYPE'];
            $value = $dsc['SHORT_DESCRIPTION_STRUCTURE']["VALUE"] ? $dsc['SHORT_DESCRIPTION_STRUCTURE']["VALUE"] : $dsc['VALUE'];

            if ($type == \CCatalogDiscount::TYPE_PERCENT) {
               $strDiscountValue = roundEx($value, CATALOG_VALUE_PRECISION) . "%";
            } elseif ($type == \CCatalogDiscount::TYPE_SALE) {
               $strDiscountValue = '= ' . \CCurrencyLang::CurrencyFormat($value, $dsc["CURRENCY"], true);
            } else {
               $strDiscountValue = \CCurrencyLang::CurrencyFormat($value, $dsc["CURRENCY"], true);
            }
            $discount[] = array("ID" => $dsc['ID'], 'NAME' => $dsc['NAME'] . '[' . $strDiscountValue . '] - ' . ($dsc['LID'] ? $dsc['LID'] : $dsc['SITE_ID']));
         }
      }
      return $discount;
   }

   public static function SaveParams($tps, $arFields = array()) {
      $temp = call_user_func_array(array($tps, "GetDesc"), array($arFields));
      return $temp;
   }

   public static function PathFilterHandler($value) {
      if ($value == '.' || $value == '..')
         return false;
      return $value;
   }

   public function includeProfiles() {
      $typeFiles = scandir(__DIR__ . $this->typeLocation);
      $typeFiles = self::PathFilter($typeFiles);
      $prof = array();
      $profiles = array();
      foreach ($typeFiles as $file) {
         if (!is_null($file)) {
            $ext = explode(".", $file);
            if ($ext[1] == 'php' && file_exists(__DIR__ . $this->typeLocation . "/" . $file)) {
               require(__DIR__ . $this->typeLocation . "/" . $file);
               $prof[] = $profiles;
            }
         }
      }
      $typeLocalLocation = Loader::getDocumentRoot() . '/local/modules/' . $this->module_id . '/type';
      if (file_exists($typeLocalLocation)) {
         $typeFiles = scandir($typeLocalLocation);
         $typeFiles = $this->PathFilter($typeFiles);
         foreach ($typeFiles as $file) {
            if (!is_null($file)) {
               if (file_exists($typeLocalLocation . "/" . $file)) {
                  require($typeLocalLocation . "/" . $file);
                  $prof[] = $profiles;
               }
            }
         }
      }
      return $prof;
   }

   public function PathFilter($arPath) {
      if (!is_array($arPath))
         return array();
      return array_filter($arPath, array($this, 'PathFilterHandler'));
   }

   public function SelectGetTypes($name, $val) {
      $profileTypes = array();
      foreach ($this->INSTALL_PROFILE as $file) {
         $profileTypes[] = call_user_func_array(array($file, "GetID"), array());
      }
      $html = '<select name="' . $name . '">';
      $html .= '<option value="">' . htmlspecialcharsex(Loc::getMessage('IBLOCK_VALUE_ANY')) . '</option>';
      foreach ($profileTypes as $tps) {
         $html .= '<option value="' . $tps['ID'] . '"';
         if ($val == $tps['ID'])
            $html .= " selected";
         $html .= '>';
         $html .= htmlspecialcharsex($tps['NAME']) . '</option>';
      }
      $html .= '</select>';
      return $html;
   }

   public function isD7() {
      $isOrderConverted = Option::get("main", "~sale_converted_15", 'N');
      return ($isOrderConverted == 'Y');
   }

   public function GetBonusClass($option = 'N') {
      return ($option == 'Y') ? "ITRound\\Vbchbbonus\\TmpTable" : "ITRound\\Vbchbbonus\\BonusTable";
   }

   public static function GetUserGroup() {
      $result = array();
      $dbResultList = \CGroup::GetList($by = "id", $order = "asc", array("ACTIVE" => "Y"));
      while ($r = $dbResultList->Fetch()) {
         $result[] = array('ID' => $r['ID'], 'NAME' => $r['NAME']);
      }
      return $result;
   }

   public function GetProfileParams($val = array()) {
      if (self::CheckArray($val)) {
         foreach ($val as $key => &$val_v) {
            $val_v = self::CheckSerialize($val_v);
            unset($val_v);
            if ($key == 'NOTIFICATION') {
               if (self::CheckArray($val['NOTIFICATION'])) {
                  foreach ($val['NOTIFICATION'] as $key_n => $v_n) {
                     $val[$key_n] = $v_n;
                  }
                  unset($val['NOTIFICATION']);
               }
            } elseif ($key == 'FILTER') {
               if (self::CheckArray($val['FILTER'])) {
                  foreach ($val['FILTER'] as $key_n => $v_n) {
                     $val[$key_n] = $v_n;
                  }
                  unset($val['FILTER']);
               }
            } elseif ($key == 'BONUSCONFIG') {
               if (self::CheckArray($val['BONUSCONFIG'])) {
                  foreach ($val['BONUSCONFIG'] as $key_n => $v_n) {
                     $val[$key_n] = $v_n;
                  }
                  unset($val['BONUSCONFIG']);
               }
            } elseif ($key == 'SETTINGS') {
               if (self::CheckArray($val['SETTINGS'])) {
                  foreach ($val['SETTINGS'] as $key_n => $v_n) {
                     $val[$key_n] = $v_n;
                  }
                  unset($val['SETTINGS']);
               }
            }
         }
      }
      return $val;
   }

   public function GetProfileFields($type) {
      if (!is_null($type)) {
         $fldsP = call_user_func_array(array($type, "GetVisualParams"), array());
         $newParam = array();
         foreach ($fldsP as $key => $pp) {
            foreach ($pp['ELEM'] as $key => $val) {
               $newParam[$key] = $val;
            }
         }
      } else
         $newParam = array();
      return self::CurFldsAr($newParam);
   }

   private function CurFldsAr($arr = array(), &$new = array()) {
      if ($this->CheckArray($arr)) {
         unset($arr['HEADER'], $arr['TIMESTAMP']);
         foreach ($arr as $ar_key => $ar_val) {
            if (array_key_exists("ELEMENT", $ar_val) && $this->CheckArray($ar_val['ELEMENT'])) {
               $this->CurFldsAr($ar_val['ELEMENT'], $new[$ar_key]);
            } else {
               $new[$ar_key] = "";
            }
         }
      }
      return $new;
   }

   public function GetSiteList() {
      $arSites = array();
      $arSiteList = array();
      $arSS = array();
      $dbSites = Main\SiteTable::getList(array(
                  'filter' => array("ACTIVE" => "Y"),
                  'select' => array("LID", "NAME"),
                  'cache' => array(
                      'ttl' => 60,
                      'cache_joins' => true,
                  )
      ));
      while ($arSite = $dbSites->fetch()) {
         $arSS[] = array('ID' => $arSite['LID'], 'NAME' => $arSite['NAME']);
         $arSites[] = $arSite;
         $arSiteList[] = $arSite['LID'];
      }
      return array('S' => $arSites, 'SL' => $arSiteList, 'LIST' => $arSS);
   }

   public function GetSiteID($default = "s1") {
      $request = Context::getCurrent()->getRequest();
      $domain = $request->getHttpHost();
      $domain = str_replace(array('https://', 'http://', '/'), '', $domain);
      $domain = explode(":", $domain);
      if (filter_var($domain[0], FILTER_VALIDATE_IP)) {
         $newdomain = $domain[0];
      } else {
         $domain = explode(".", $domain[0]);
         if (sizeof($domain) > 2) {
            $newdomain[] = $domain[sizeof($domain) - 2];
            $newdomain[] = $domain[sizeof($domain) - 1];
         } else
            $newdomain = $domain;
      }
      $filter = array("DOMAIN" => implode(".", $newdomain));
      $rsSites = Main\SiteDomainTable::getList(array(
                  'filter' => $filter,
                  'select' => array('LID'),
                  'cache' => array(
                      'ttl' => 60,
                      'cache_joins' => true,
                  )
              ))->fetch();
      if (self::CheckArray($rsSites)) {
         return $rsSites["LID"];
      } else
         return $default;
   }

   public function GetSiteSID() {
      $site = array();
      $filter = array("ACTIVE" => "Y");

      $rsSites = Main\SiteTable::getList(array(
                  'filter' => $filter,
                  'select' => array("LID"),
      ));
      while ($arSite = $rsSites->fetch()) {
         $site[] = $arSite['LID'];
      }
      return $site;
   }

   public function declOfNum($number, $words = array(), $cur = "") {
      if (floatval($number) < 0)
         $ot = true;
      else
         $ot = false;
      $cases = array(2, 0, 1, 1, 1, 2);
      if ($number < 0)
         $number = $number * (-1);
      if ($this->CheckArray(array_filter($words))) {
         $Value = ($ot ? "-" : "") . round($number, 2) . " " . $words[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
      } else {
         if ($cur) {
            $Value = ($ot ? "-" : "") . FormatCurrency(round($number, 2), $cur);
         } else {
            $Value = ($ot ? "-" : "") . round($number, 2);
         }
      }
      return $Value;
   }

   public function GetUserInfo($UserID) {
      $username = "";
      $phone = "";
      $UserInfo = Main\UserTable::getList(array(
                  'filter' => array("ID" => $UserID),
                  'select' => array("NAME", "LAST_NAME", "EMAIL", "LID", "LOGIN"),
                  'cache' => array(
                      'ttl' => 60,
                      'cache_joins' => true,
                  )
              ))->fetch();
      if ($UserInfo) {
         if ($UserInfo["NAME"])
            $username .= $UserInfo["NAME"];
         if ($UserInfo["LAST_NAME"])
            $username .= ' ' . $UserInfo["LAST_NAME"];
         if ($UserInfo["PERSONAL_PHONE"])
            $phone = $UserInfo["PERSONAL_PHONE"];
      }
      if (trim($username) == "")
         $username = $UserInfo["LOGIN"];
      return array("FIO" => trim($username), "EMAIL" => $UserInfo["EMAIL"], "LID" => ($UserInfo["LID"] ? $UserInfo["LID"] : $this->SITE_ID), 'USERPHONE' => $phone);
   }

   public function AuthorizedUser() {
      return $this->arUSER['AUTH'];
   }

   public function GetPropInfo($propID = array()) {
      $result = array();
      if (sizeof($propID) > 0) {
         foreach ($propID as $prID) {
            $properties = \CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "ID" => $prID));
            while ($prop = $properties->GetNext()) {
               $result[$prop['IBLOCK_ID']] = $prop['ID'];
            }
         }
         return $result;
      } else
         return false;
   }

   public function GetOptions($site, $OPTION = "") {
      if ($OPTION) {
         $l = Option::get($this->module_id, $OPTION, "", $site);
         return array("OPTION" => $this->CheckSerialize($l), "SUFFIX" => $site);
      } else
         return false;
   }

   public function CheckSerialize($str = '') {
      if (is_string($str)) {
         if (base64_decode($str, true) === false) {
            if (self::CheckArray(unserialize($str))) {
               return unserialize($str);
            } else
               return $str;
         } else {
            if (self::CheckArray(unserialize(base64_decode($str)))) {
               return unserialize(base64_decode($str));
            } else
               return $str;
         }
      } else
         return $str;
   }

   public static function CheckArray($arr) {
      return (is_array($arr) ? (sizeof($arr) > 0) : false);
   }

   public static function array_filter_recursive($input) {
      foreach ($input as &$value) {
         if (is_array($value)) {
            $value = self::array_filter_recursive($value);
         }
         unset($value);
      }
      return array_filter($input);
   }

   public function FilterArray($arr) {
      if ($this->CheckArray($arr))
         return $arr = array_filter($arr);
   }

   public function GetWordForm($count, $singleForm, $someForm = FALSE, $manyForm = FALSE) {
      if ($someForm == FALSE) {
         $someForm = $singleForm;
      }
      if ($manyForm == FALSE) {
         $manyForm = $someForm;
      }
      $yearDeclension = new \Bitrix\Main\Grid\Declension($singleForm, $someForm, $manyForm);
      return $count . " " . $yearDeclension->get($count);
   }

   public function ReturnParams() {
      $k = $this->GetModuleOptions();
      $params = [];
      foreach ($k as $h => $l) {
         if (strpos($h, 'HEAD') === false) {
            $params[] = $h;
         }
      }
      return $params;
   }

   public function GetAddUserReferalOption() {
      return array(
          'SITE' => array(
              'WIDGET' => new SiteWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_ADD_REF_SITECHANGE'),
              'DEFAULT' => 'N',
              "REQUIRED" => true,
              "TYPE" => "string",
              "MULTIPLE" => false,
          ),
      );
   }

   public function GetAddAccountOption() {
      return array(
          'SCOREIN' => array(
              'WIDGET' => new CheckboxWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_SCOREIN'),
              'DEFAULT' => 'N',
              "REQUIRED" => false,
              "TYPE" => "string",
              "MULTIPLE" => false,
          ),
      );
   }

   public function GetFromInnerOption() {
      return array(
          'BONUSINNERIN' => array(
              'WIDGET' => new BonusInnerWidget(),
              "TITLE" => Loc::getMessage('VBCH_BONUSINNER_IN'),
              'DEFAULT' => '',
          ),
      );
   }

   public function GetAddBonusOption() {
      return array(
          'BONUS' => array(
              "WIDGET" => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSCNT') . ' ' . Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
              "DEFAULT" => "",
              'REQUIRED' => true,
              'PLACEHOLDER' => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_BONUSDESC'),
              "TYPE" => "string",
              "SIZE" => 35,
              "MAXLENGHT" => 50
          ),
          'USERGROUP' => array(
              'WIDGET' => new UsergroupWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_USERGROUP'),
              'DEFAULT' => '',
              "REQUIRED" => false,
              "TYPE" => "",
              "MULTIPLE" => true,
              "SIZE" => 10,
          ),
          'ACTIVATE' => array(
              'WIDGET' => new CheckboxWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_ACTIVUSER'),
              'DEFAULT' => 'Y',
              "REQUIRED" => true,
              "TYPE" => "string",
              "MULTIPLE" => false,
          ),
          'DESCRIPTION' => array(
              'WIDGET' => new TextboxWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_DESCRIPTION'),
              'DEFAULT' => '',
              "REQUIRED" => false,
              "TYPE" => "string",
              "MULTIPLE" => false,
          ),
      );
   }

   public function GetAllOptions() {
      return Option::getForModule("vbcherepanov.bonus", $this->SITE_ID);
   }

   public function GetModuleOptions() {
      return array(
          'HEAD' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BON_SYS'),
          ),
          'BONUSNAME' => array(
              'WIDGET' => new BonusNameWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BON_NAME'),
              'DEFAULT' => array(),
          ),
          'ENABLE_OLD_PAY_FUNCTION' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('ENABLE_OLD_PAY_FUNCTION'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          'CHECKSOA' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('DISABLED_CHECK_SALEORDERAJAX'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          'BNRTRNORDCNCL' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BNRTRNORDCNCL'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          'BNSELEMENTCART' => array(
              'WIDGET' => new ComboboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_ELEM_SHOW'),
              'VARIANT' => array(
                  array('ID' => 'SUMM', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_ELEM_SHOW1')),
                  array('ID' => 'MIN', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_ELEM_SHOW2')),
                  array('ID' => 'MAX', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_ELEM_SHOW3')),
              ),
              'MULTIPLE' => false,
              'SIZE' => 5,
              'DEFAULT' => 'N',
          ),
          'WITHBASKET' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_WITHBASKET'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          "CHECKPAYORDER" => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_CHECKPAYORDER'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          "CHECKORDERSTATUS" => array(
              'WIDGET' => new OrderStatus(),
              "TITLE" => Loc::getMessage('VBCHBONUS_OPTION_TITLE_CHECKORDERSTATUS'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'AFTERDAY' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFTERDAY'),
              'DEFAULT' => array(),
          ),
          'BONUSINORDERMAIL' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSINORDERMAIL'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          'STARTBONUINORDERPROP' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_STARTBONUINORDERPROP'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          'DATEFORMAT' => array(
              'WIDGET' => new DateFormatWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_DATEFORMAT'),
              'DEFAULT' => '',
              'SIZE' => '25',
          ),
          'SALEPROFILETYPE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_SALEPROFILETYPE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_SALEPROFILETYPE_DEFAULT'),
              'PLACEHOLDER' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_SALEPROFILETYPE_DESC'),
              'SIZE' => 100,
          ),
          'BONUSPAYPROFILETYPE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSPAYPROFILETYPE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSPAYPROFILETYPE_DEFAULT'),
              'PLACEHOLDER' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSPAYPROFILETYPE_DESC'),
              'SIZE' => 100,
          ),
          'CHECKSMSBONUSPAY' => array(
              'WIDGET' => new CheckboxWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_CHECKSMSBONUSPAY'),
              'DEFAULT' => 'N',
              "REQUIRED" => false,
              "TYPE" => "string",
              "MULTIPLE" => false,
          ),
          'MONTHSTATSMS' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_MONTHSTATSMS'),
              'DEFAULT' => 'N',
              "TYPE" => "string",
          ),
          'MONTHSTATSMSTEMPLATE' => array(
              'WIDGET' => new HtmleditorWidget(),
              "TITLE" => Loc::getMessage('VBCHBB_TYPE_PROFILE_ELEMENT_MONTHSTATSMSTEMPLATE'),
              'DEFAULT' => '',
              'HEIGHT' => '400',
              'BODY_TYPE' => 'text',
              'WIDTH' => '100%',
              "REQUIRED" => false,
              "TYPE" => "string",
              "MULTIPLE" => false,
          ),
          'HEAD4' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD4'),
          ),
          'SOCIAL' => array(
              'WIDGET' => new SocialSetupWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_SOCIAL'),
          ),
          'HEAD6' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD6'),
          ),
          'REFACTIVE' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_REFACTIVE'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'REFFIRST' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_REFFIRST'),
              'DEFAULT' => 'REF',
              'SIZE' => '12',
              'PLACEHOLDER' => 'REF',
          ),
          'REFLEN' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_REFLEN'),
              'DEFAULT' => '',
              'SIZE' => '5',
              'PLACEHOLDER' => '',
          ),
          'REFPARAM' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_REFPARAM'),
              'DEFAULT' => '',
              'SIZE' => '5',
              'PLACEHOLDER' => '',
          ),
          'NEW_USER_EMAIL' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_NEW_USER_EMAIL'),
              'DEFAULT' => 'NEW_USER',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          "REFLEVELCOUNT" => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_REFLEVELCOUNT'),
              'DEFAULT' => '',
              'SIZE' => '5',
              'PLACEHOLDER' => '',
          ),
          "COOKIETIME" => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COOKIETIME'),
              'DEFAULT' => 365,
              'SIZE' => '5',
              'PLACEHOLDER' => '',
          ),
          'TYPEREFCODE' => array(
              'WIDGET' => new ComboboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_TYPEREFCODE'),
              'VARIANT' => array(
                  array('ID' => 'NUM', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_NUM')),
                  array('ID' => 'ABS', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_ABS')),
                  array('ID' => 'NUMABS', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_NUMABS')),
                  array('ID' => 'NUMNEXT', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_NUMNEXT')),
                  array('ID' => 'PHONE', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COMP_PHONE')),
              ),
              'MULTIPLE' => false,
              'SIZE' => 5,
              'DEFAULT' => 'N',
          ),
          'HEAD8' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD8'),
          ),
          'AFFILIATE_AUTO_GENERATE_COUPON' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFFILIATE_AUTO_GENERATE_COUPON'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'AFFILIATE_AUTO_GENERATE_COUPON_DISC' => array(
              'WIDGET' => new ActionWidget(),
              "TITLE" => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFFILIATE_AUTO_GENERATE_COUPON_DISC'),
              'DEFAULT' => '',
              "MULTIPLE" => false,
              'SIZE' => 5,
          ),
          'AFFILIATE_AUTO_GENERATE_AFF' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFFILIATE_AUTO_GENERATE_AFF'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'AFFILIATE_AUTO_GENERATE_AFF_GROUP' => array(
              'WIDGET' => new UsergroupWidget(),
              "TITLE" => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFFILIATE_AUTO_GENERATE_AFF_GROUP'),
              'DEFAULT' => '',
              "REQUIRED" => false,
              "TYPE" => "",
              "MULTIPLE" => true,
              "SIZE" => 6,
          ),
          'AFFILIATE_AUTO_ONES_COUPON' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFFILIATE_AUTO_ONES_COUPON'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'AFFILIATE_COUPON_PHONE' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_AFFILIATE_COUPON_PHONE'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'HEAD81' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD81'),
          ),
          'COUPONE_MASK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COUPONE_MASK'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'NOTE_MASK' => array(
              'WIDGET' => new NoteWidget(),
              'TITLE' => '',
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_COUPONE_MASK_DESC'),
          ),
          'HEAD9' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD9'),
          ),
          'BONUSCARD_REG_CREATE' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSCARD_REG_CREATE'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'BONUSCARD_DEACTIVE' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSCARD_DEACTIVE'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'BONUSCARD_ACCOUNTS' => array(
              'WIDGET' => new \ITRound\Vbchbbonus\BonusInnerWidget(),
              "TITLE" => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSCARD_ACCOUNTS'),
              'DEFAULT' => '',
          ),
          'BONUSCARD_STARTBONUS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUSCARD_STARTBONUS'),
              'DEFAULT' => '0',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'BONUCARD_PHONE' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_BONUCARD_PHONE'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'HEAD7' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD7'),
          ),
          'USE_RESTAPI' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_USE_RESTAPI'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'SUPPORT_USE_LOG' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_SUPPORT_USE_LOG'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'ONLY_HTTPS_EXCHANGE' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_ONLY_HTTPS_EXCHANGE'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'USE_AUTH_BY_LOGIN_PASSWORD' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_USE_AUTH_BY_LOGIN_PASSWORD'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'USE_AUTH_TOKEN' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_USE_AUTH_TOKEN'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'RESTAPI_TOKEN' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_TOKEN'),
              'DEFAULT' => \ITRound\Vbchbbonus\CBonusRestAPI::generateTokens(),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'IDENTUSER' => array(
              'WIDGET' => new ComboboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_IDENT_USER'),
              'VARIANT' => array(
                  array('ID' => 'PHONE', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_IDENT_USER_PHONE')),
                  array('ID' => 'EMAIL', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_IDENT_USER_EMAIL')),
                  array('ID' => 'EXTERNAL', 'NAME' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_RESTAPI_IDENT_USER_EXTERNALID')),
              ),
              'MULTIPLE' => false,
              'SIZE' => 5,
              'DEFAULT' => 'N',
          ),
          'HEAD10' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD10'),
          ),
          'HEAD101' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL'),
          ),
          'INTEGRATIONDETAIL' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAILON'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONDETAIL_MAIN_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_MAIN_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONDETAIL_OUT_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_OUT_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONDETAIL_COMP_TEMPLATE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_COMP_TEMPLATE'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONDETAIL_LANG_BONUS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS_DEF'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'HEAD102' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDCART'),
          ),
          'INTEGRATIONCARTON' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAILON'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONCART_MAIN_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTBONPAY_MAIN_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONCART_OUT_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTBONPAY_OUT_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONCARTON_BONUS_OFFER' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTON_BONUS_OFFER'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONCART_OFFER_LANG_BONUS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS_DEF'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONCART_LANG_BONUS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS_ORDER'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS_DEF_ORDER'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONCARTBONUSPAYON' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTBONUSPAY'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONCARTBONPAY_MAIN_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTBONPAY_MAIN_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONCARTBONPAY_OUT_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTBONPAY_OUT_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONCARTBONPAY_COMP_TEMPLATE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONCARTBONPAY_COMP_TEMPLATE'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'HEAD103' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDER'),
          ),
          'INTEGRATIONORDER' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAILON'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONORDER_MAIN_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDER_MAIN_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDER_OUT_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDER_OUT_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDER_LANG_BONUS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS_ORDER'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUS_DEF_ORDER'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDER_LANG_BONUSPAY' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUSPAY_ORDER'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONDETAIL_LANG_BONUSPAY_DEF_ORDER'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDER_BONUSPAY_MAIN_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDER_BONUSPAY_MAIN_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDER_BONUSPAY_OUT_BLOCK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDER_BONUSPAY_OUT_BLOCK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_PATH_TO_HELP' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_PATH_TO_HELP'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_TEXT_TO_HELP' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_TEXT_TO_HELP'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_TEXT_IN_YOU_ACCOUNT' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_TEXT_IN_YOU_ACCOUNT'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_TXT_YOU_MUST_PAY' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_TXT_YOU_MUST_PAY'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_TXT_HOW_MUCH_BONUS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_TXT_HOW_MUCH_BONUS'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_TXT_BONUSPAY_CANCEL' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_TXT_BONUSPAY_CANCEL'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'ITR_BONUS_TXT_BONUSPAY_OK' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('ITR_BONUS_TXT_BONUSPAY_OK'),
              'DEFAULT' => '',
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDERBONUSSHOW' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSSHOW'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONORDERBONUSPAYSHOW' => array(
              'WIDGET' => new CheckboxWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSPAYSHOW'),
              'DEFAULT' => '',
              "TYPE" => "string",
          ),
          'INTEGRATIONORDERBONUSTITLE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSTITLE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSTITLE_DEFAULT'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDERBONUSPAYTITLE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSPAYTITLE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSPAYTITLE_DEFAULT'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDERBONUSPAYTITLE' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSPAYTITLE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERHEADERBONUSPAYTITLE_DEFAULT'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDERSMS' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERSMSTITLE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERSMS'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'INTEGRATIONORDERNOPAY' => array(
              'WIDGET' => new TextWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERNOPAYTITLE'),
              'DEFAULT' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INTEGRATIONORDERNOPAY'),
              'SIZE' => '50',
              'PLACEHOLDER' => '',
          ),
          'HEAD52' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD52'),
          ),
          'HEAD1' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD1'),
          ),
          'INOUTMAILTEMP' => array(
              'WIDGET' => new MailTemplateWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INOUTMAILTEMP'),
          ),
          'HEAD2' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD2'),
          ),
          'INOUTAGENT' => array(
              'WIDGET' => new AgentSetupWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INOUTAGENT'),
          ),
          'HEAD3' => array(
              'WIDGET' => new HeaderWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_HEAD3'),
          ),
          'INOUTEVENT' => array(
              'WIDGET' => new EventsSetupWidget(),
              'TITLE' => Loc::getMessage('VBCHBONUS_OPTION_TITLE_INOUTEVENT')
          ),
      );
   }

   public function SaveOption($site, $name, $post) {
      global $APPLICATION;
      if ($name == "ELEMENTFILTER") {
         if (Loader::includeModule("catalog")) {
            $obCond = new \ITRound\Vbchbbonus\CITRBBFilterCatalogCondTree();
            $boolCond = $obCond->Init(BT_COND_MODE_PARSE, 0, array());
            if (!$boolCond) {
               if ($ex = $APPLICATION->GetException()) {
                  echo $ex->GetString() . "<br>";
               }
            }
            $post = $obCond->Parse($post);
         }
      }
      $post = ($this->CheckArray($post)) ? serialize($post) : $post;
      Option::set($this->module_id, $name, $post, $site);
   }

   public function InstallMailTemplate($type = "", $SITE_ID) {
      $template = array(
          "ADD" => array("ACTIVE" => "Y",
              "EVENT_NAME" => $this->mail_temp_prefix,
              "LID" => "",
              "EMAIL_TO" => "#USEREMAIL#",
              "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
              "BCC" => "#BCC#",
              "SUBJECT" => "",
              "BODY_TYPE" => "text",
              "MESSAGE" => "",
          ),
          "DELETE" => array("ACTIVE" => "Y",
              "EVENT_NAME" => $this->mail_temp_prefix,
              "LID" => "",
              "EMAIL_TO" => "#USEREMAIL#",
              "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
              "BCC" => "#BCC#",
              "SUBJECT" => "",
              "BODY_TYPE" => "text",
              "MESSAGE" => "",
          ),
          "PAY" => array("ACTIVE" => "Y",
              "EVENT_NAME" => $this->mail_temp_prefix,
              "LID" => "",
              "EMAIL_TO" => "#USEREMAIL#",
              "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
              "BCC" => "#BCC#",
              "SUBJECT" => "",
              "BODY_TYPE" => "text",
              "MESSAGE" => "",
          ),
          "STATISTIC" => array("ACTIVE" => "Y",
              "EVENT_NAME" => $this->mail_temp_prefix,
              "LID" => "",
              "EMAIL_TO" => "#USEREMAIL#",
              "EMAIL_FROM" => "#BCC#",
              "BCC" => "",
              "SUBJECT" => "",
              "BODY_TYPE" => "text",
              "MESSAGE" => "",
          ),
          "PREDELETE" => array("ACTIVE" => "Y",
              "EVENT_NAME" => $this->mail_temp_prefix,
              "LID" => "",
              "EMAIL_TO" => "#USEREMAIL#",
              "EMAIL_FROM" => "#BCC#",
              "BCC" => "",
              "SUBJECT" => "",
              "BODY_TYPE" => "text",
              "MESSAGE" => "",
          ),
      );
      $langs = \Bitrix\Main\Localization\LanguageTable::getList(array(
                  'select' => array('LID'),
      ));
      while ($lang = $langs->fetch()) {
         $lid = $lang["LID"];
         $sites = Main\SiteTable::getList(array(
                     'filter' => array("LANGUAGE_ID" => $lid),
                     'select' => array("LID", "NAME"),
         ));
         $et = new \CEventType;
         $q = $et->GetList(array('LID' => $lid, 'EVENT_NAME' => $this->mail_temp_prefix), array())->Fetch();
         if (!$q) {
            $et->Add(array(
                "LID" => $lid,
                "EVENT_NAME" => $this->mail_temp_prefix,
                "NAME" => Loc::getMessage("VBCHBB_EVENT_TYPE_NAME"),
                "DESCRIPTION" => Loc::getMessage("VBCHBB_EVENT_TYPE_DESC")
                    )
            );
         }
         if ($SITE_ID) {
            if ($type) {
               $MES = $template[$type];
               return $this->CreateEmailTemp($SITE_ID, $type, $MES);
            } else {
               foreach ($template as $evnt => $arr) {
                  $this->CreateEmailTemp($SITE_ID, $evnt, $arr);
               }
            }
         }
         unset($et);
      }
      return true;
   }

   private function CreateEmailTemp($lid, $l, $a) {
      $emess = new \CEventMessage;
      $k1 = 0;
      $a["LID"] = $lid;
      $a["SUBJECT"] = Loc::getMessage("VBCHBB_EVENT_SUBJECT_" . $l);
      $a["MESSAGE"] = Loc::getMessage("VBCH_SENDQUE_EVENT_BODY_" . $l);
      $k1 = $emess->Add($a);
      $this->SaveOption($lid, $this->mail_events_prefix . '_' . $l, $k1);
      return $k1;
   }

   function UnInstallPostTemplate() {
      $SS = self::GetSiteList();
      foreach ($SS['SL'] as $lid) {
         $tmp = $this->ReturnMailTemplate($lid);
         foreach ($tmp as $pm) {
            if ($pm['ID'])
               \CEventMessage::Delete($pm['ID']);
         }
      }
      $et = new \CEventType;
      $et->Delete($this->mail_temp_prefix);
      unset($et);
      return true;
   }

   public function GetStatusMailTemplate($ID) {
      if ($ID) {
         $rsEM = \CEventMessage::GetByID(intval($ID))->Fetch();
         return $rsEM;
      }
   }

   public function AddALLBonus($uid, $settings = array()) {
      $settings['BONUS'] = floatval($settings['BONUS']);
      if ($settings['BONUS'] != 0 && $settings['BONUS'] != '') {
         $userID = array();
         global $USER;
         $arFilter = array(
             'GROUPS_ID' => $settings['USERGROUP'],
             'ACTIVE' => $settings['ACTIVE'],
             'ID' => intval($uid)
         );
         $settings['DESCRIPTION'] = trim(htmlspecialchars($settings['DESCRIPTION']));
         if (strlen($settings['DESCRIPTION']) == 0) {
            if (floatval($settings['BONUS']) > 0) {
               $settings['DESCRIPTION'] = Loc::getMessage('VBCHBB_ALLBONUS_ADD');
            } elseif (floatval($settings['BONUS']) < 0) {
               $settings['DESCRIPTION'] = Loc::getMessage('VBCHBB_ALLBONUS_DELETE');
            }
         }
         $Utmp = \CUser::GetList($by, $order, $arFilter, array('SELECT' => array("ID")));
         while ($q = $Utmp->Fetch()) {
            $userID[] = $q['ID'];
         }
         $res = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                     'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $this->SITE_ID),
         ));
         while ($prof = $res->fetch()) {
            $l = $this->CheckSerialize($prof['NOTIFICATION']);
            $l['TRANSACATIONMESSAGE'] = $settings['DESCRIPTION'];
            $prof['NOTIFICATION'] = base64_encode(serialize($l));
            $check = ($prof['ISADMIN'] == 'Y');
            $check = ($check) ? $USER->isAdmin() : $check;
            if ($check) {
               foreach ($userID as $ui) {
                  $this->AddBonus(array('bonus' => $settings['BONUS'],
                      'ACTIVE' => 'Y',
                      'ACTIVE_FROM' => '',
                      'ACTIVE_TO' => '',
                      'CURRENCY' => ''),
                          array('SITE_ID' => $this->SITE_ID,
                              'USER_ID' => $ui,
                              'ORDER_ID' => '',
                              'IDUNITS' => 'EDIT_ACCOUNT' . $ui . '_' . $settings['BONUS'] . '_' . time(),
                              'DESCRIPTION' => $settings['DESCRIPTION']),
                          $prof,
                          true);
                  Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
               }
            }
         }
      }
   }

   public function FromInner($cur, $Settings) {
      global $USER;
      $res = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                  'filter' => array('ACTIVE' => 'Y', 'TYPE' => 'BONUS', 'SITE' => $this->SITE_ID),
      ));
      while ($prof = $res->fetch()) {
         $check = ($prof['ISADMIN'] == 'Y');
         $check = ($check) ? $USER->isAdmin() : $check;
         if ($check) {

            $this->AddBonus(array('bonus' => $cur['CURRENT_BUDGET'],
                'ACTIVE' => 'Y',
                'ACTIVE_FROM' => '',
                'ACTIVE_TO' => '',
                'CURRENCY' => ''),
                    array('SITE_ID' => $this->SITE_ID,
                        'USER_ID' => $cur['USER_ID'],
                        'IDUNITS' => 'EDIT_ACCOUNT' . $cur['USER_ID'] . '_' . $cur['CURRENT_BUDGET'] . '_' . time(),
                        'ORDER_ID' => '',
                        'DESCRIPTION' => $Settings['DESC'],
                        'BONUSACCOUNTSID' => $Settings['BONUSACCOUNTSID']
                    ),
                    $prof,
                    true);
            Vbchbbonus\CvbchbonusprofilesTable::ProfileIncrement($prof['ID']);
         }
      }
   }

   public function PayBonusOrder($OrderID, $bonus = 0, $paytype) {
      $OrderID = intval($OrderID);
      $bonus = floatval($bonus);

      if ($OrderID != 0 && $bonus != 0) {
         $currentBudget = 0;
         $bon1 = 0;
         $bon2 = 0;
         $arFields = $this->GetArrayForProfile(0, array(), 1, false, $OrderID);
         $this->SITE_ID = $arFields['LID'];
         $pf = 0;
         if ($this->CheckSiteOn()) {
            $PAYPROF = $this->GetBonusPayProfile($arFields);
            $propm = array();
            if ($this->CheckArray($PAYPROF)) {
               $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
               foreach ($PAYPROF as $prof) {
                  $lp = array();
                  $settings = $this->CheckSerialize($prof['SETTINGS']);
                  $bonusConfig = $this->CheckSerialize($prof['BONUSCONFIG']);
                  $ac = $bonusConfig['BONUSINNERIN']['BONUSINNER'] ? $bonusConfig['BONUSINNERIN']['BONUSINNER'] : $bonusConfig['BONUSINNEROUT']['BONUSINNER'];
                  if ($ac == 0 || $ac == '') {
                     $ac = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
                     $f = Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                                 'filter' => array('PAYSYSTEMID' => $ac),
                                 'select' => array('ID'),
                             ))->fetch();
                     $ac = $f['ID'];
                  }
                  $MAXPAY = intval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
                  if ($paytype == 'innerpay') {
                     $bon2 = $bonus;

                     $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $arFields['USERID'], 'CURRENCY' => $arFields['CURRENCY']));
                     if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $CB1 = round($arUserAccount['CURRENT_BUDGET'], 2);
                        if ($CB1 < $MAXPAY)
                           $MAXPAY = $CB1;
                        if ($bon2 >= $MAXPAY)
                           $bon2 = $MAXPAY;
                        if ($CB1 > 0 && $bon2 > $CB1)
                           $bon2 = 0;
                     }
                  } else {
                     $bon2 = $arFields['SUM_PAID'];
                  }
                  $bon2_s = $this->Bonusis($bonusConfig, $bon2, "off");
                  $currentBudget += $bon2;
                  if ($bon2 > 0) {
                     $pf = $pf + floatval($bon2_s);
                     if ($settings['BONUSFORDISCOUNT'] != 'Y') {
                        Vbchbbonus\CvbchBonusPayment::InnerPay($arFields['ORDER_ID'], $bon2_s, $pf);
                     }

                     $this->AddBonus(array('bonus' => -$bon2, 'ACTIVE' => 'Y', 'ACTIVE_FROM' => '', 'ACTIVE_TO' => '', 'CURRENCY' => ''), array('DESCRIPTION' => 'PAY PART ORDER#' . $arFields['ORDER_ID'], 'SITE_ID' => $arFields['LID'], 'USER_ID' => $arFields['USERID'], 'ORDER_ID' => $arFields['ORDER_ID'], 'IDUNITS' => 'PAY_PART_' . $arFields['ORDER_ID'], 'MAIL_TEMP' => 'PAY'), $prof, false, false);
                     $trans = Vbchbbonus\TmpTable::getList(
                                     array(
                                         'order' => array('SORT' => 'ASC', 'ACTIVE_TO' => 'DESC'),
                                         'filter' => array('ACTIVE' => "Y", ">BONUS" => 0, "USERID" => $arFields['USERID']),
                                         'select' => array('*'),
                                     )
                             )->fetchAll();
                     $this->Raspred($bon2, $trans);
                     foreach ($trans as $tr) {
                        $l = $tr;
                        unset($l['ID'], $l['UPDATE_DATE']);
                        Vbchbbonus\TmpTable::update($tr['ID'], $l);
                     }
                  }
                  if ($paytype = 'bonuspay') {
                     $bon1 = $bonus;
                     if ($bon1 > 0 && $bon1 <= $arFields['ORDER_PRICE']) {
                        $dbUserAccount = Vbchbbonus\AccountTable::getList(array(
                                    'filter' => array('USER_ID' => $arFields['USERID'], 'BONUSACCOUNTSID' => $ac),
                                ))->fetch();
                        $CB = round($dbUserAccount["CURRENT_BUDGET"], 2);
                        $CB = floatval($CB);
                        if ($CB < $MAXPAY)
                           $MAXPAY = $CB;
                        if ($bon1 >= $MAXPAY)
                           $bon1 = $MAXPAY;
                        if ($CB > 0 && $bon1 > $CB)
                           $bon1 = 0;
                        $currentBudget += $bon1;
                     }
                     $bon1_s = $this->Bonusis($bonusConfig, $bon1, "off");
                     $pf = $pf + floatval($bon1_s);
                     if ($settings['BONUSFORDISCOUNT'] != 'Y') {
                        Vbchbbonus\CvbchBonusPayment::InnerBonusPay($arFields['ORDER_ID'], $bon1_s, $pf);
                     }
                     $this->AddBonus(array('bonus' => -$bon1, 'ACTIVE' => 'Y', 'ACTIVE_FROM' => '', 'ACTIVE_TO' => '', 'CURRENCY' => ''), array('DESCRIPTION' => 'PAY PART ORDER#' . $arFields['ORDER_ID'], 'ORDER_ID' => $arFields['ORDER_ID'], 'SITE_ID' => $arFields['LID'], 'USER_ID' => $arFields['USERID'], 'ORDER_ID' => $arFields['ORDER_ID'], 'IDUNITS' => 'PAY_PART_' . $arFields['ORDER_ID'], 'MAIL_TEMP' => 'PAY'), $prof, false, false);
                     $trans = Vbchbbonus\BonusTable::getList(
                                     array(
                                         'order' => array('SORT' => 'ASC', 'ACTIVE_TO' => 'DESC'),
                                         'filter' => array('ACTIVE' => "Y", ">BONUS" => 0, "USERID" => $arFields['USERID'], 'BONUSACCOUNTSID' => $ac),
                                         'select' => array('*'),
                                     )
                             )->fetchAll();
                     $this->Raspred($bon1, $trans);
                     foreach ($trans as $tr) {
                        $l = $tr;
                        unset($l['ID'], $l['UPDATE_DATE']);
                        Vbchbbonus\BonusTable::update($tr['ID'], $l);
                     }
                  }
                  if ($settings['ORDERPROPBONUSPAY']['ACTIVE'] == 'Y') {
                     $propm = array_merge($propm, $settings['ORDERPROPBONUSPAY']['ID']);
                  }
               }
               $propID = $this->ReturnOrderProp($arFields['PERSON_TYPE_ID'], $propm);
               if ($propID) {
                  $this->AddOrderProperty($propID, $currentBudget, $arFields['ORDER_ID']);
               }
               if ($settings['BONUSFORDISCOUNT'] != 'Y') {
                  if ($arFields['ORDER_ID'] > 0 && $currentBudget > 0) {
                     if ($this->isD7()) {
                        \CSaleOrder::Update($arFields['ORDER_ID'], array('SUM_PAID' => $currentBudget, 'USER_ID' => $arFields['USERID']));
                        if ($currentBudget == $arFields['ORDER_PRICE']) {
                           \CSaleOrder::PayOrder($arFields["ORDER_ID"], "Y", false, false);
                        }
                     } else {
                        \CSaleOrder::Update($arFields['ORDER_ID'], array('SUM_PAID' => $currentBudget, 'USER_ID' => $arFields['USERID']));
                        if ($currentBudget == $arFields['ORDER_PRICE']) {
                           \CSaleOrder::PayOrder($arFields["ORDER_ID"], "Y", false, false);
                        }
                     }
                  }
               }
            }
         }
      }
   }

   public function ReplaceRefPhone($uid, $settings) {
      $ra['OPTION'] = 'N';
      if ($this->CheckArray($settings)) {
         $ra = $this->GetOptions($settings['SITE_ID'], 'REFACTIVE');
         $allrefuser = \ITRound\Vbchbbonus\CVbchRefTable::getList(
                         array(
                             'select' => array('ID', 'USERID'),
                             'filter' => array('LID' => $settings['SITE_ID'], 'USERID' => $uid),
                         )
                 )->fetch();
      }
      if ($ra['OPTION'] == 'Y') {
         if ($allrefuser) {
            $REF_OPTION['REFFIRST'] = $this->GetOptions($settings['SITE_ID'], 'REFFIRST');
            $REF_OPTION['REFLEN'] = $this->GetOptions($settings['SITE_ID'], 'REFLEN');
            $REF_OPTION['REFPARAM'] = $this->GetOptions($settings['SITE_ID'], 'REFPARAM');
            $REF_CODE = Vbchbbonus\Vbchreferal::GenerateRef($REF_OPTION['REFFIRST']['OPTION'], intval($REF_OPTION['REFLEN']['OPTION']), $uid);
            if ($REF_CODE != '')
               Vbchbbonus\CVbchRefTable::update($allrefuser['ID'], ['REFERER' => $REF_CODE]);
         }
      }
   }

   public function AddAllReferalCode($uid, $settings, $unique = false) {
      $ra['OPTION'] = 'N';
      if ($this->CheckArray($settings)) {
         $ra = $this->GetOptions($settings['SITE_ID'], 'REFACTIVE');
         $allrefuser = \ITRound\Vbchbbonus\CVbchRefTable::getList(
                         array(
                             'select' => array('USERID'),
                             'filter' => array('LID' => $settings['SITE_ID'], 'USERID' => $uid),
                         )
                 )->fetch();

         if ($allrefuser) {
            $create = false;
         } else {
            $create = true;
         }
      }
      if ($unique)
         $create = true;
      if ($ra['OPTION'] == 'Y') {
         if ($create) {
            $REF_OPTION['REFFIRST'] = $this->GetOptions($settings['SITE_ID'], 'REFFIRST');
            $REF_OPTION['REFLEN'] = $this->GetOptions($settings['SITE_ID'], 'REFLEN');
            $REF_OPTION['REFPARAM'] = $this->GetOptions($settings['SITE_ID'], 'REFPARAM');
            $REF_CODE = Vbchbbonus\Vbchreferal::GenerateRef($REF_OPTION['REFFIRST']['OPTION'], intval($REF_OPTION['REFLEN']['OPTION']), $uid);
            Vbchbbonus\CVbchRefTable::add(array(
                'LID' => $settings['SITE_ID'],
                'ACTIVE' => 'Y',
                'REFERER' => $REF_CODE,
                'USERID' => $uid,
                'COOKIE' => $cck = Vbchbbonus\Vbchreferal::uniqKey('refbonus_'),
            ));
         }
      }
   }

   public function AddAccounts($uid, $settings = array()) {
      if ($settings['SCOREIN'] == 'Y') {
         if (Loader::includeModule('sale')) {
            $by = 'ID';
            $order = 'desc';
            $dbResultList = \CSaleUserAccount::GetList(
                            array($by => $order),
                            array('=USER_ID' => $uid),
                            false,
                            false,
                            array("ID")
            );
            if ($arAccountList = $dbResultList->Fetch()) {
               $create = false;
            } else {
               $create = true;
            }
         }
      } else {
         $arID = \ITRound\Vbchbbonus\AccountTable::getList(
                         array(
                             'filter' => array('USER_ID' => $uid),
                             'select' => array('USER_ID')
                         )
                 )->fetch();
         if ($arID) {
            $create = false;
         } else {
            $create = true;
         }
      }
      if ($settings['SCOREIN'] == 'Y') {
         if (Loader::includeModule('sale')) {
            if ($create)
               \CSaleUserAccount::UpdateAccount($uid, 0, 'RUB', "MANUAL", 0);
         }
      } else {
         if ($create) {
            if (array_key_exists('BONUSACCOUNTSID', $settings) && $settings['BONUSACCOUNTSID']) {
               $ac = $settings['BONUSACCOUNTSID'];
            } else {
               $ac = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
               $f = \ITRound\Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                           'filter' => array('PAYSYSTEMID' => $ac),
                           'select' => array('ID'),
                       ))->fetch();
               $ac = $f['ID'];
            }
            \ITRound\Vbchbbonus\AccountTable::add(
                    array(
                        'USER_ID' => $uid,
                        'CURRENT_BUDGET' => 0,
                        'CURRENCY' => 'RUB',
                        'BONUSACCOUNTSID' => $ac
                    )
            );
         }
      }
   }

   public function ReturnMailTemplate($site = '') {
      if ($site == '')
         $site = $this->SITE_ID;
      return array(
          array('ID' => $this->GetOptions($site, $this->mail_events_prefix . '_ADD'), 'TYPE' => 'ADD'),
          array('ID' => $this->GetOptions($site, $this->mail_events_prefix . '_PAY'), 'TYPE' => 'PAY'),
          array('ID' => $this->GetOptions($site, $this->mail_events_prefix . '_DELETE'), 'TYPE' => 'DELETE'),
          array('ID' => $this->GetOptions($site, $this->mail_events_prefix . '_PREDELETE'), 'TYPE' => 'PREDELETE'),
          array('ID' => $this->GetOptions($site, $this->mail_events_prefix . '_STATISTIC'), 'TYPE' => 'STATISTIC'),
      );
   }

   public function ReturnEvents() {
      $defaultEvents = array(
          array(
              'MODULE_FROM' => "sale",
              'MESSAGE_ID' => "OnOrderNewSendEmail",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnOrderNewSendEmail",
              'ACTIVE' => "N",
          ),
          array(
              'MODULE_FROM' => "main",
              'MESSAGE_ID' => "OnProlog",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnProlog",
              'ACTIVE' => "N",
          ),
          array(
              'MODULE_FROM' => "main",
              'MESSAGE_ID' => "OnPageStart",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnPageStartRestApi",
              'ACTIVE' => "N",
          ),
          array(
              'MODULE_FROM' => "main",
              'MESSAGE_ID' => "OnBeforeProlog",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnBeforePrologHandler",
              'ACTIVE' => "N",
          ),
      );
      $ReferEvent = array(
          array(
              'MODULE_FROM' => "main",
              'MESSAGE_ID' => "OnAfterUserAdd",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnRegUser",
              'ACTIVE' => "N",
          ),
          array(
              'MODULE_FROM' => "main",
              'MESSAGE_ID' => "OnAfterUserRegister",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnRegUser",
              'ACTIVE' => "N",
          ),
          array(
              'MODULE_FROM' => "main",
              'MESSAGE_ID' => "OnAfterUserSimpleRegister",
              'TO_CLASS' => "CVbchbbEvents",
              'TO_METHOD' => "OnRegUser",
              'ACTIVE' => "N",
          ),
      );
      $defaultEvents[] = array(
          'MODULE_FROM' => 'sale',
          'MESSAGE_ID' => 'OnSaleComponentOrderOneStepComplete',
          'TO_CLASS' => 'CVbchbbEvents',
          'TO_METHOD' => 'OnSaleComponentOrderOneStepComplete',
          'ACTIVE' => 'N',
      );
      $defaultEvents[] = array(
          'MODULE_FROM' => 'main',
          'MESSAGE_ID' => 'OnBeforeEndBufferContent',
          'TO_CLASS' => 'CVbchbbEvents',
          'TO_METHOD' => 'EndToEndIntegration',
          'ACTIVE' => 'N',
      );

      $defaultEvents[] = array(
          'MODULE_FROM' => 'sale',
          'MESSAGE_ID' => 'onSaleAdminOrderInfoBlockShow',
          'TO_CLASS' => '\ITRound\Vbchbbonus\ITROrderAdminHeader',
          'TO_METHOD' => 'orderAdminHeader',
          'ACTIVE' => 'N',
      );
      $defaultEvents[] = array(
          'MODULE_FROM' => 'main',
          'MESSAGE_ID' => 'OnAdminSaleOrderViewDraggable',
          'TO_CLASS' => '\ITRound\Vbchbbonus\EditAdminOrderBonusClass',
          'TO_METHOD' => 'onInit',
          'ACTIVE' => 'N',
      );
      $defaultEvents[] = array(
          'MODULE_FROM' => 'main',
          'MESSAGE_ID' => 'OnAdminSaleOrderCreateDraggable',
          'TO_CLASS' => '\ITRound\Vbchbbonus\EditAdminOrderBonusClass',
          'TO_METHOD' => 'onInit',
          'ACTIVE' => 'N',
      );
      $defaultEvents[] = array(
          'MODULE_FROM' => 'main',
          'MESSAGE_ID' => 'OnAdminSaleOrderEditDraggable',
          'TO_CLASS' => '\ITRound\Vbchbbonus\EditAdminOrderBonusClass',
          'TO_METHOD' => 'onInit',
          'ACTIVE' => 'N',
      );

      if ($this->NEW_SALE_ORDER_AJAX) {
         $defaultEvents[] = array(
             'MODULE_FROM' => 'sale',
             'MESSAGE_ID' => 'OnSaleComponentOrderResultPrepared',
             'TO_CLASS' => 'CVbchbbEvents',
             'TO_METHOD' => 'OnSaleComponentOrderOneStepProcess',
             'ACTIVE' => 'N',
         );
         $defaultEvents[] = array(
             'MODULE_FROM' => 'sale',
             'MESSAGE_ID' => 'OnSaleComponentOrderCreated',
             'TO_CLASS' => 'CVbchbbEvents',
             'TO_METHOD' => 'OnSaleComponentOrderCreated',
             'ACTIVE' => 'N',
         );
      }
      $refOption = $this->GetOptions($this->SITE_ID, "REFACTIVE");
      if ($refOption['OPTION'] == 'Y') {
         $defaultEvents = array_merge($defaultEvents, $ReferEvent);
      }
      $tmpEvents = array();
      if ($this->CheckArray($this->MODULE_EVENTS)) {
         foreach ($this->MODULE_EVENTS as $event) {
            if ($this->CheckArray($event)) {
               foreach ($event as $key => $ev) {
                  if (array_key_exists('RULES', $ev) && eval('return ' . $ev['RULES'])) {
                     $bitrix = explode("_", $ev['BITRIX']);
                     $module = explode("::", $ev['MODULE']);
                     $check = false;
                     foreach ($tmpEvents as $idf => $edc) {
                        if ($edc['MESSAGE_ID'] == $bitrix[1] && $edc['TO_METHOD'] == $module[1]) {
                           $check = true;
                           break;
                        }
                     }
                     if (!$check)
                        $tmpEvents[] = array(
                            'MODULE_FROM' => $bitrix[0],
                            'MESSAGE_ID' => $bitrix[1],
                            'TO_CLASS' => $module[0],
                            'TO_METHOD' => $module[1],
                            'ACTIVE' => $event['ACTIVE'],
                        );
                  }
               }
            }
         }
      }
      $tmpEvents = array_merge($tmpEvents, $defaultEvents);
      $Events = array();
      $newEvents = array();
      Loader::includeModule("perfmon");
      $obTable = new \CPerfomanceTable;
      $obTable->Init("b_module_to_module");
      foreach ($tmpEvents as $idvv => $tmpEv) {
         $arFilter = array(
             'TO_MODULE_ID' => $this->module_id,
             'FROM_MODULE_ID' => $tmpEv['MODULE_FROM'],
             'MESSAGE_ID' => $tmpEv['MESSAGE_ID'],
             'TO_CLASS' => str_replace("\\", "\\\\", $tmpEv['TO_CLASS']),
             'TO_METHOD' => $tmpEv['TO_METHOD']
         );
         $tmp = $tmpEv;
         $rsData = $obTable->GetList(array("*"), $arFilter)->Fetch();
         if ($rsData) {
            $tmp['ACTIVE'] = 'Y';
         } else {
            $tmp['ACTIVE'] = 'N';
         }
         $newEvents[] = $tmp;
         unset($tmp, $rsData);
      }
      $newEvents = array_map("unserialize", array_unique(array_map("serialize", $newEvents)));
      return $newEvents;
   }

   public function GetProfileTypeFromFunc($string = null) {
      $l = array();
      if ($this->CheckArray($this->MODULE_EVENTS)) {
         foreach ($this->MODULE_EVENTS as $prof => $event) {
            if ($this->CheckArray($event)) {
               foreach ($event as $key => $ev) {
                  if (trim($ev['MODULE']) == trim($string) && (array_key_exists('RULES', $ev) && eval('return ' . $ev['RULES']))) {
                     $l[] = $prof;
                  }
               }
            }
         }
      }
      return $l;
   }

   public function ReturnAgents() {
      $tmpAgents = array();
      $agent_defaults = array(
          array(
              "ACTIVE" => "N",
              "MODULE_ID" => $this->module_id,
              "FUNC" => "CVbchbbEvents::BonusLive",
              "NEXT_EXEC" => "",
              "AGENT_INTERVAL" => "86400",
          ),
          array(
              "ACTIVE" => "N",
              "MODULE_ID" => $this->module_id,
              "FUNC" => "CVbchbbEvents::BonusStatistic",
              "NEXT_EXEC" => "",
              "AGENT_INTERVAL" => "2592000",
          ),
          array(
              "ACTIVE" => "N",
              "MODULE_ID" => $this->module_id,
              "FUNC" => "CVbchbbEvents::BonusActive",
              "NEXT_EXEC" => "",
              "AGENT_INTERVAL" => "86400",
          )
      );
      if ($this->CheckArray($this->MODULE_AGENTS)) {
         foreach ($this->MODULE_AGENTS as $agent) {
            if ($this->CheckArray($agent)) {
               foreach ($agent as $key => $ag) {
                  $tmpAgents[] = array(
                      "ACTIVE" => $ag['ACTIVE'],
                      "MODULE_ID" => ($ag['MODULE'] == 'this' ? $this->module_id : $ag['MODULE']),
                      "FUNC" => $ag['NAME'],
                      "NEXT_EXEC" => "",
                      "AGENT_INTERVAL" => $ag['AGENT_INTERVAL'],
                  );
               }
            }
         }
      }
      $tmpAgents = array_merge($tmpAgents, $agent_defaults);
      $agents = array();
      if ($this->CheckArray($tmpAgents)) {
         foreach ($tmpAgents as $agtns) {
            $res = \CAgent::GetList(Array("ID" => "DESC"), array("MODULE_ID" => $agtns['MODULE_ID'], 'NAME' => $agtns['FUNC'] . "();"))->Fetch();
            if ($res) {
               $agents[] = array(
                   'ID' => $res['ID'],
                   'FUNC' => $res['NAME'],
                   'ACTIVE' => $res['ACTIVE'],
                   'LAST_EXEC' => $res['LAST_EXEC'],
                   'NEXT_EXEC' => $res['NEXT_EXEC'],
                   'AGENT_INTERVAL' => $res['AGENT_INTERVAL'],
               );
            } else {
               $agentID = \CAgent::AddAgent($agtns['FUNC'] . "();", $agtns['MODULE_ID'], "N", $agtns['AGENT_INTERVAL'], "", $agtns['ACTIVE'], "", 100);
               if ($agentID) {
                  $res = \CAgent::GetById($agentID)->Fetch();
                  $agents[] = array(
                      'ID' => $res['ID'],
                      'FUNC' => $res['NAME'],
                      'ACTIVE' => $res['ACTIVE'],
                      'LAST_EXEC' => $res['LAST_EXEC'],
                      'NEXT_EXEC' => $res['NEXT_EXEC'],
                      'AGENT_INTERVAL' => $res['AGENT_INTERVAL'],
                  );
               }
            }
         }
      }
      return $agents;
   }

   public function DeleteAgents() {
      $agents = $this->ReturnAgents();
      if ($this->CheckArray($agents)) {
         foreach ($agents as $agtns) {
            \CAgent::Delete($agtns['ID']);
         }
      }
   }

   public static function SetFunctionList($name, $value = "", $multiple = false, $size = 5, $type = array()) {
      $property = array();
      $properties = \CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"), Array("ACTIVE" => "Y"));
      while ($prop_fields = $properties->GetNext()) {
         $res = \CIBlock::GetList(array(), array("ID" => $prop_fields["IBLOCK_ID"]), false)->Fetch();
         if (self::CheckArray($type)) {
            if (in_array($prop_fields['PROPERTY_TYPE'], $type))
               $property[$res["NAME"]][$prop_fields["ID"]] = $prop_fields["NAME"];
         } else
            $property[$res["NAME"]][$prop_fields["ID"]] = $prop_fields["NAME"];
      }
      unset($properties, $res);
      if ($multiple) {
         $name .= "[]";
         $mpl = 'multiple="multiple"';
      } else {
         $mpl = "";
      }
      $html = "<select size='" . $size . "' name='" . $name . "'" . $mpl . ">";
      foreach ($property as $ibname => $prop) {
         $html .= "<optgroup label='" . $ibname . "'>";
         foreach ($prop as $id => $name) {
            if ($multiple && is_array($value)) {

               $check = in_array($id, $value) ? 'selected' : '';
            } else {
               $check = ($value == $id) ? 'selected' : '';
            }
            $html .= "<option value='" . $id . "' " . $check . ">" . $name . "</option>";
         }
         $html .= "</optgroup>";
      }
      $html .= "</select>";
      unset($property, $id, $name);
      return $html;
   }

   public function GetIblockList() {
      if (Loader::includeModule('iblock')) {
         $res = \CIBlock::GetList(Array(), Array('SITE_ID' => $this->SITE_ID, 'ACTIVE' => 'Y'), false);
         $arIblock = array();
         while ($ar_res = $res->Fetch()) {
            $arIblock['REFERENCE'][] = "[" . $ar_res["IBLOCK_TYPE_ID"] . "] - " . $ar_res["NAME"];
            $arIblock['REFERENCE_ID'][] = $ar_res["ID"];
         }
         array_unique($arIblock);
         unset($res, $ar_res);
         return $arIblock;
      }
   }

   public function GetHlList() {
      if (Loader::includeModule('highloadblock')) {
         $arHL = array();
         $res = \Bitrix\Highloadblock\HighloadBlockTable::getList(
                         array(
                             'filter' => array(),
                         )
         );
         while ($r = $res->fetch()) {
            $arHL['REFERENCE'][] = $r["NAME"];
            $arHL['REFERENCE_ID'][] = $r["ID"];
         }
      }
      return $arHL;
   }

   public function GetBlogList() {
      if (Loader::includeModule('blog')) {
         $arBlog = array();
         $SORT = Array("NAME" => "ASC");
         $arFilter = Array("ACTIVE" => "Y", "GROUP_SITE_ID" => $this->SITE_ID);
         $arSelectedFields = array("ID", "NAME");
         $dbBlogs = \CBlog::GetList($SORT, $arFilter, false, false, $arSelectedFields);
         while ($ar_Blog = $dbBlogs->Fetch()) {
            $arBlog['REFERENCE'][] = $ar_Blog["NAME"];
            $arBlog['REFERENCE_ID'][] = $ar_Blog["ID"];
         }
         array_unique($arBlog);
         return $arBlog;
      }
   }

   public function GetForumList() {
      if (Loader::includeModule('forum')) {
         $arForum = array();
         $arFilter["ACTIVE"] = "Y";
         $arOrder = array("SORT" => "ASC", "NAME" => "ASC");
         $db_Forum = \CForumNew::GetList($arOrder, $arFilter);
         while ($ar_Forum = $db_Forum->Fetch()) {
            $arForum['REFERENCE'][] = $ar_Forum["NAME"];
            $arForum['REFERENCE_ID'][] = $ar_Forum["ID"];
         }
         array_unique($arForum);
         return $arForum;
      }
   }

   public function GetSocial() {
      $social = array();
      $params = self::GetOptions($this->SITE_ID, "SOCIAL");
      $params = $params['OPTION'];
      if (self::CheckArray($params)) {
         foreach ($params as $SC => $val) {
            if ($val['ACTIVE'] == 'Y') {
               $social['REFERENCE'][] = $val['NAME'];
               $social['REFERENCE_ID'][] = $SC;
            }
         }
      }
      return $social;
   }

   public function runSocialBonus($social, $URL, $postID) {
      if ($this->CheckSiteOn()) {
         global $USER;
         $UID = $USER->GetID();
         if (!$UID)
            return array('OK' => 0, 'ERROR' => 'BADUSER');
         $prof = CvbchbonusprofilesTable::getList(array(
                     'filter' => array('ACTIVE' => "Y", "TYPE" => 'SOCIAL')
                 ))->fetchAll();
         if ($this->CheckArray($prof)) {
            foreach ($prof as $profile) {
               $filter = $this->CheckSerialize($profile['FILTER']);
               if (array_key_exists('TYPESOURCE', $filter)) {
                  if ($filter['TYPESOURCE'] == $social) {
                     if ($this->CheckSocial($social, $URL)) {
                        $this->AddBonTYPE('SOCIAL', $profile, array('USERID' => $UID, 'ID' => $social . '_' . $postID . '_' . $URL));
                        $bonus = $this->BonusParams($profile['BONUS'], $this->CheckSerialize($profile['BONUSCONFIG']));
                        Vbchbbonus\CvbchbonussocpushTable::Add(array(
                            'SOCIAL' => $social,
                            'USER_ID' => $UID,
                            'SOCIALTEXT' => '',
                        ));
                        $bonus['bonus'] = $this->GetAllBonus($bonus['bonus'], $bonus['bonus'], true);
                        return array('OK' => 1, 'BONUS' => $bonus['bonus']);
                     }
                  }
               }
            }
         } else {
            return array('OK' => 0, 'ERROR' => 'BADPROFILES');
         }
      }
   }

   public function CheckSocial($social, $path) {
      global $USER;
      $UID = $USER->GetID();
      $ok = true;
      if (!$UID)
         return false;
      foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $cls) {
         $res = call_user_func_array(array($cls, "getList"), array(array(
                 'filter' => array("USERID" => $UID, "TYPES" => 'SOCIAL'))
                 ))->fetchAll();
         if ($this->CheckArray($res)) {
            foreach ($res as $bon) {
               $opt = $this->CheckSerialize($bon['OPTIONS']);
               $tmp = explode("_", $opt['IDUNITS']);
               $ok = ($social == $tmp[0] && $path == $tmp[2]) ? false : true;
               return $ok;
            }
         }
      }
      return $ok;
   }

   public function GetSubscribeList() {
      if (Loader::includeModule('subscribe')) {
         $arOrder = Array("SORT" => "ASC", "NAME" => "ASC");
         $arFilter = Array("ACTIVE" => "Y", "LID" => $this->SITE_ID);
         $rsRubric = \CRubric::GetList($arOrder, $arFilter);
         $arRubrics = array();
         while ($arRubric = $rsRubric->GetNext()) {
            $subscribe['SUBSCRIBE'][] = array('ID' => $arRubric['ID'], 'NAME' => $arRubric['NAME']);
         }
      }
      if (Loader::includeModule('sender')) {
         $dataListDb = \Bitrix\Sender\MailingTable::getList(array(
                     "filter" => array("ACTIVE" => "Y", "SITE_ID" => $this->SITE_ID),
         ));
         while ($arRes = $dataListDb->fetch())
            $subscribe['SENDER'][] = array('ID' => $arRes['ID'], 'NAME' => $arRes['NAME']);
      }
      return $subscribe;
   }

   public function GetMailEvent() {
      $event = array();
      $dbType = \CEventMessage::GetList($by = "ID", $order = "DESC", array('ACTIVE' => 'Y'));
      while ($arType = $dbType->GetNext()) {
         $event['REFERENCE'][] = "[" . $arType["ID"] . "] " . $arType["SUBJECT"];
         $event['REFERENCE_ID'][] = $arType["ID"];
      }
      return $event;
   }

   public static function GetOrderStatus() {
      $status = array();
      if (\Bitrix\Main\Loader::includeModule("sale")) {

         $by = $order = "";
         $dbResultList = \CSaleStatus::GetList(array($by => $order), array("LID" => LANG),
                         false,
                         false,
                         array('ID', 'SORT', 'TYPE', 'NOTIFY', 'LID', 'NAME', 'DESCRIPTION', $by)
         );
         while ($st = $dbResultList->Fetch()) {
            $status['REFERENCE'][] = "[" . $st['ID'] . "]" . $st['NAME'];
            $status['REFERENCE_ID'][] = $st['ID'];
         }
      }
      return $status;
   }

   public function GetPersonType() {
      $result = array();
      if (Loader::includeModule('sale')) {
         $dbResultList = \CSalePersonType::GetList(array("ID" => "ASC"));
         while ($r = $dbResultList->Fetch()) {
            $result[] = array("ID" => $r['ID'], 'NAME' => $r['NAME']);
         }
      }
      return $result;
   }

   public function GetDelivery() {
      $result = array();
      if (Loader::includeModule('sale')) {
         if ($this->isD7()) {
            $res = \Bitrix\Sale\Delivery\Services\Table::getList(array('filter' => array('ACTIVE' => 'Y')));
            while ($dev = $res->Fetch()) {
               $result[] = array("ID" => $dev['ID'], 'NAME' => $dev['NAME']);
            }
         } else {
            $dbResultList = \CSaleDelivery::GetList(
                            array(
                                "SORT" => "ASC",
                                "NAME" => "ASC"
                            ),
                            array(
                                "LID" => $this->SITE_ID,
                                "ACTIVE" => "Y",
                            ),
                            false,
                            false,
                            array("ID", "NAME")
            );

            while ($r = $dbResultList->Fetch()) {
               $result[] = array("ID" => $r['ID'], 'NAME' => $r['NAME']);
            }
            $res = \CSaleDeliveryHandler::GetList(array(), array("LID" => $this->SITE_ID, "ACTIVE" => "Y"));
            while ($dev = $res->Fetch()) {
               $result[] = array("ID" => $dev['ID'], 'NAME' => $dev['NAME']);
            }
         }
         $result = array_filter($result);
      }
      return $result;
   }

   public function GetPaysystem() {
      $result = array();
      if (Loader::includeModule('sale')) {
         $res = \CSalePaySystem::GetList(array("NAME" => "ASC"));
         while ($r = $res->Fetch()) {
            $result[] = array("ID" => $r['ID'], 'NAME' => $r['NAME']);
         }
      }
      return $result;
   }

   public function GetAllBonus($bonus, $summa = 0, $minusbonus = false, $cnt = 1) {
      $delta1 = 0;
      if ($bonus == 0)
         return 0;
      $percent = strpos($bonus, "%");
      if ($percent !== false) {
         $per = floatval($bonus);
         $bonus = $summa * ($per / 100); //get bonus  (summa)
         if ($minusbonus) {
            $delta1 = $summa - $bonus;
            $bonus = $delta1 * ($per / 100); //get bonus (summa-bonus)
         }
         return $bonus;
      } else {
         return $bonus * $cnt;
      }
   }

   public function BonusParams($bonus, $Settings = array()) {
      $minus = ($bonus < 0);
      $timeRepl = array('C' => 'hours', 'D' => 'days', 'N' => 'weeks', 'M' => 'months', 'L' => 'years', 'W' => 'weeks', 'Y' => 'years');
      $OPTION['BONUSNAME'] = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
      $OPTION['DATEFORMAT'] = $this->GetOptions($this->SITE_ID, 'DATEFORMAT');
      $BONUSIS = floatval(($Settings['BONUSIS'] != "") ? $Settings['BONUSIS'] : 1);
      $bonus = floatval($bonus * $BONUSIS);
      $percision = intval($Settings['PRECISION'] ? $Settings['PRECISION'] : 0);
      if ($Settings['ROUND'] == 'PHP_ROUND_HALF_UP')
         $l = PHP_ROUND_HALF_UP;
      if ($Settings['ROUND'] == 'PHP_ROUND_HALF_DOWN')
         $l = PHP_ROUND_HALF_DOWN;
      if ($Settings['ROUND'] == 'PHP_ROUND_HALF_EVEN')
         $l = PHP_ROUND_HALF_EVEN;
      if ($Settings['ROUND'] == 'PHP_ROUND_HALF_ODD')
         $l = PHP_ROUND_HALF_ODD;
      $bonus = round($bonus, $percision, $l);
      $date = new \Bitrix\Main\Type\DateTime;
      $currenttime = clone $date;
      $Newbonus = array(
          'bonus' => floatval($bonus),
          'ACTIVE' => 'Y',
          'ACTIVE_FROM' => '',
          'ACTIVE_TO' => '',
          'CURRENCY' => ''
      );

      if ($Settings['DELAY']['ACTIVE'] == 'Y') {
         $activeFrom = $date->add($Settings['DELAY']['COUNT'] . ' ' . $timeRepl[$Settings['DELAY']['PERIOD']]);
         $Newbonus['ACTIVE'] = 'N';
         $Newbonus['ACTIVE_FROM'] = ($minus) ? '' : $activeFrom; //->format('Y-m-d H:i:s');
      } else {
         $Newbonus['ACTIVE_FROM'] = $currenttime; //->format('Y-m-d H:i:s');
         $activeFrom = $currenttime;
      }
      if ($minus) {
         $Newbonus['ACTIVE_FROM'] = $currenttime; //->format('Y-m-d H:i:s');
         $Newbonus['ACTIVE'] = 'Y';
      }
      $act = clone $activeFrom;
      if ($Settings['TIMELIFE']['PERIOD'] == 'A') {
         $Newbonus['ACTIVE_TO'] = "";
      } else {
         $Newbonus['ACTIVE_TO'] = $act->add($Settings['TIMELIFE']['COUNT'] . ' ' . $timeRepl[$Settings['TIMELIFE']['PERIOD']]);
      }
      if ($Newbonus['ACTIVE_TO']) {
         $Newbonus['ACTIVE_TO'] = ($minus) ? '' : $Newbonus['ACTIVE_TO'];
      }
      $Newbonus['CURRENCY'] = $this->ModuleCurrency($Settings['BONUSINNERIN']['BONUSINNER']);
      return $Newbonus;
   }

   public function ReturnBonus($ORDER_ID) {
      $retbon = $this->GetOptions($this->SITE_ID, "BNRTRNORDCNCL");
      $bonuspayprofile = $this->GetOptions($this->SITE_ID, "BONUSPAYPROFILETYPE");
      $bonuspayprofile = explode(",", $bonuspayprofile['OPTION']);
      if ($retbon['OPTION'] == 'Y') {
         $IDUNITS = "PAY_PART_" . $ORDER_ID;
         $SelectBONUS = array();
         $return = true;
         foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $cls) {
            $res = call_user_func_array(array($cls, "getList"), array(array(
                    'filter' => array('ACTIVE' => 'Y')
                )))->fetchAll();
            if ($this->CheckArray($res)) {
               foreach ($res as $bv) {
                  $dopmor = $this->CheckSerialize($bv['OPTIONS']);
                  if ($dopmor['IDUNITS'] == 'RETURN_PAY_PART_' . $ORDER_ID && in_array($bv['TYPES'], $bonuspayprofile)) {
                     return false;
                  }
               }
               foreach ($res as $bon) {
                  $dopmor = $this->CheckSerialize($bon['OPTIONS']);
                  if ($dopmor['IDUNITS'] == $IDUNITS) {
                     $SelectBONUS = $bon;
                     break;
                  }
               }
            }
         }
         $Fields = $SelectBONUS;
         $bonus = ($SelectBONUS['BONUS'] < 0) ? $SelectBONUS['BONUS'] * (-1) : $SelectBONUS['BONUS'];
         $Fields['BONUS'] = $bonus;
         $opttmp = $this->CheckSerialize($SelectBONUS['OPTIONS']);
         $opttmp['IDUNITS'] = 'RETURN_PAY_PART_' . $ORDER_ID;
         $Fields['OPTIONS'] = serialize($opttmp);
         unset($Fields['ID'], $Fields['TIMESTAMP_X']);
         $profile = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                     'filter' => array('ACTIVE' => 'Y', 'ID' => $opttmp['PROFILE_ID'], 'SITE' => $this->SITE_ID),
                 ))->fetchall();
         if ($this->CheckArray($profile)) {
            foreach ($profile as $profil) {
               if ($return) {
                  if ($profil['SCOREIN'] == 'Y') {
                     call_user_func_array(array('\\ITRound\\Vbchbbonus\\TmpTable', "add"), array($Fields));
                     $arOldUserAccount = \CSaleUserAccount::GetByID($Fields['USERID']);
                     $USER_ID = $arOldUserAccount["USER_ID"];
                     $CURRENCY = $arOldUserAccount["CURRENCY"];
                     $OLD_BUDGET = DoubleVal($arOldUserAccount["CURRENT_BUDGET"]);
                     $updateSum = $OLD_BUDGET + $Fields['BONUS'];
                     $currentbudget = $updateSum;
                     \CSaleUserAccount::UpdateAccount($USER_ID, $updateSum, $CURRENCY, "Return bonus for pay part order" . $ORDER_ID);
                  } else {
                     call_user_func_array(array('\\ITRound\\Vbchbbonus\\BonusTable', "add"), array($Fields));
                     $BonusAccount = Vbchbbonus\AccountTable::getList(array(
                                 'filter' => array('USER_ID' => $Fields['USERID']),
                     ));
                     if ($bonAc = $BonusAccount->fetch()) {
                        $currentbudget = floatval($bonAc['CURRENT_BUDGET'] + $Fields['BONUS']);
                        $newFlds = $bonAc;
                        unset($newFlds['ID']);
                        $newFlds['CURRENT_BUDGET'] = $currentbudget;
                        Vbchbbonus\AccountTable::update($bonAc['ID'], $newFlds);
                     }
                  }
                  $option1 = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
                  $FIELDS['MAIL_TYPE'] = 'ADD';
                  $FIELDS['BONUS'] = $this->ReturnCurrency($FIELDS['BONUS']);
                  $FIELDS['BONUS_ALL'] = $this->ReturnCurrency($currentbudget);
                  $this->Notification($FIELDS, $this->CheckSerialize($profil['NOTIFICATION']));
               }
            }
         }
      }
   }

   private function GetReplaceData($fields) {
      \Bitrix\Main\Loader::includeModule("sale");
      $usr = $this->GetUserInfo($fields['USERID']);
      if ($fields['ORDER_ID']) {
         $k = [];
         $ord = \Bitrix\Sale\Order::load($fields['ORDER_ID']);
         $k[0] = $ord->getField('ACCOUNT_NUMBER');
         $k[2] = $ord->getField('DATE_INSERT');
         $k[3] = $ord->getField('PRICE');
      }

      $userBon = Vbchbbonus\AccountTable::getList(array(
                  'filter' => array('USER_ID' => $fields['USERID'], "BONUSACCOUNTSID" => $fields['ac']),
                  'cache' => array(
                      'ttl' => 60,
                      'cache_joins' => true,
                  )
      ));
      if ($acc_bon = $userBon->fetch()) {
         $ALLBONUS = $fields['BONUS'] + $acc_bon['CURRENT_BUDGET'];
      } else {
         $ALLBONUS = $fields['BONUS'];
      }
      $replace = array(
          '#ORDER_ID#',
          '#ORDER_NUMBER#',
          '#ORDER_DATE#',
          '#ORDER_PRICE#',
          '#USER_ID#',
          '#BONUS#',
          '#ACTIVEFROM#',
          '#ACTIVETO#',
          '#USERNAME#',
          '#USEREMAIL#',
          '#BONUSALL#'
      );
      $replace_to = array(
          $fields['ORDER_ID'],
          $k[0],
          $k[2],
          $k[3],
          $fields['USERID'],
          $this->ReturnCurrency($fields['BONUS']),
          $fields['ACTIVE_FROM'],
          $fields['ACTIVE_TO'],
          $usr['FIO'],
          $usr['EMAIL'],
          $this->ReturnCurrency($ALLBONUS),
      );
      return [$replace, $replace_to];
   }

   public function getaccountsID() {
      $ac = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
      $f = Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                  'filter' => array('ACTIVE' => "Y", 'PAYSYSTEMID' => $ac),
                  'select' => array('ID'),
                  'cache' => array(
                      'ttl' => 60,
                      'cache_joins' => true,
                  )
              ))->fetch();
      return $f['ID'];
   }

   public function AddBonus($bonus = array(), $Settings = array(), $profile, $admin = false, $ref = false) {
      if ($bonus['bonus'] != 0 || $bonus['bonus'] != '') {
         $OPTION['BONUSNAME'] = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
         $BONUSCONFIG = $this->CheckSerialize($profile['BONUSCONFIG']);
         $profile['SETTINGS'] = $this->CheckSerialize($profile['SETTINGS']);
         if ($profile['TYPE'] == 'NONE') {
            $Descr = $profile['DESCRIPTION'];
         } else {
            if (method_exists($this->INSTALL_PROFILE[$profile['TYPE']], 'getDesc'))
               $Descr = $this->INSTALL_PROFILE[$profile['TYPE']]->getDesc();
         }

         $ac = $BONUSCONFIG['BONUSINNERIN']['BONUSINNER'] ? $BONUSCONFIG['BONUSINNERIN']['BONUSINNER'] : $BONUSCONFIG['BONUSINNEROUT']['BONUSINNER'];
         if (array_key_exists('BONUSACCOUNTSID', $Settings) && ($Settings['BONUSACCOUNTSID'] != '' || $Settings['BONUSACCOUNTSID'] != 0))
            $ac = $Settings['BONUSACCOUNTSID'];
         if ($ac == 0 || $ac == '') {
            $ac = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
            $f = Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                        'filter' => array('ACTIVE' => "Y", 'PAYSYSTEMID' => $ac),
                        'select' => array('ID'),
                        'cache' => array(
                            'ttl' => 60,
                            'cache_joins' => true,
                        )
                    ))->fetch();
            $ac = $f['ID'];
         }

         $NOTIFICATIONS = $this->CheckSerialize($profile['NOTIFICATION']);
         $fld = [
             'SITEID' => $Settings['SITE_ID'] ? $Settings['SITE_ID'] : $Settings['SITEID'],
             'BONUS' => $bonus['bonus'],
             "USERID" => $Settings['USER_ID'],
             "ACTIVE_FROM" => $bonus['ACTIVE_FROM'],
             "ACTIVE_TO" => $bonus['ACTIVE_TO'],
             'ORDER_ID' => $Settings['ORDER_ID'],
             'ac' => $ac
         ];
         $repl = $this->GetReplaceData($fld);
         if ($NOTIFICATIONS['TRANSACATIONMESSAGE']) {
            $NOTIFICATIONS['TRANSACATIONMESSAGE'] = str_replace($repl[0], $repl[1], $NOTIFICATIONS['TRANSACATIONMESSAGE']);
         } else {
            $NOTIFICATIONS['TRANSACATIONMESSAGE'] = false;
         }
         $FIELDS = array(
             "LID" => $Settings['SITE_ID'],
             "BONUS" => $bonus['bonus'],
             "ACTIVE" => $bonus['ACTIVE'],
             "USERID" => $Settings['USER_ID'],
             "ACTIVE_FROM" => $bonus['ACTIVE_FROM'],
             "ACTIVE_TO" => $bonus['ACTIVE_TO'],
             "TYPES" => $profile['TYPE'],
             "SORT" => $profile['SETTINGS']['SORT'] ? $profile['SETTINGS']['SORT'] : 500,
             "PARTPAY" => 0,
             "DESCRIPTION" => $NOTIFICATIONS['TRANSACATIONMESSAGE'] ? $NOTIFICATIONS['TRANSACATIONMESSAGE'] : $profile['NAME'] . ($ref ? ' Referal ' : ''),
             "BONUSACCOUNTSID" => $ac,
             "OPTIONS" => serialize(array('IDUNITS' => (floatval($bonus['bonus']) < 0 ? $Settings['IDUNITS'] : $Settings['IDUNITS']), 'PROFILE_ID' => $profile['ID'], 'DESCRIPTION' => $Settings['DESCRIPTION'])),
         );
         foreach (GetModuleEvents($this->module_id, "OnBeforeBonusAdd", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$FIELDS));
         if (!$this->InnerCheck(intval($bonus['bonus']), array('IDUNITS' => $Settings['IDUNITS']), $profile)) {
            $this->ACCOUNT_BITRIX_CLASS = $this->GetFunction($profile['SCOREIN']);
            Application::getConnection()->startTransaction();
            $l = call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS, "add"), array($FIELDS));
            if ($l->isSuccess()) {
               Application::getConnection()->commitTransaction();
            } else {
               Application::getConnection()->rollbackTransaction();
            }
            $FIELDS['DESCRIPTION'] = $Settings['DESCRIPTION'] ? $Settings['DESCRIPTION'] : $Descr;
            $bonusall = 0;
            $cur = $this->ModuleCurrency();
            if (($BONUSCONFIG['DELAY']['ACTIVE'] != 'Y' || floatval($bonus['bonus']) < 0) || $admin) {
               if ($profile['SCOREIN'] == 'Y') {
                  \CSaleUserAccount::UpdateAccount($Settings['USER_ID'], $bonus['bonus'], $bonus['CURRENCY'], $Descr);
                  if ($ar = \CSaleUserAccount::GetByUserID($Settings['USER_ID'], $cur)) {
                     $bonusall = $this->ReturnCurrency($ar['CURRENT_BUDGET']);
                  }
               } else {
                  if ($FIELDS['ACTIVE'] == 'Y') {
                     $resbon = Vbchbbonus\AccountTable::getList(array(
                                 'filter' => array('USER_ID' => $Settings['USER_ID'], "BONUSACCOUNTSID" => $ac),
                                 'cache' => array(
                                     'ttl' => 60,
                                     'cache_joins' => true,
                                 )
                     ));
                     if ($acc_bon = $resbon->fetch()) {
                        $newFLDS = $acc_bon;
                        $userbonus = $acc_bon['CURRENT_BUDGET'];
                        $newFLDS['CURRENT_BUDGET'] = $bonus['bonus'] + $userbonus;
                        $bonusall = $this->ReturnCurrency($bonus['bonus'] + $userbonus);
                        unset($newFLDS['ID']);
                        $res = Vbchbbonus\AccountTable::update($acc_bon['ID'], $newFLDS);
                     } else {
                        $ACC_FIELDS = array(
                            'USER_ID' => $Settings['USER_ID'],
                            'CURRENT_BUDGET' => $bonus['bonus'],
                            'CURRENCY' => $cur,
                            'NOTES' => '',
                            "BONUSACCOUNTSID" => $ac,
                        );
                        $res = Vbchbbonus\AccountTable::add($ACC_FIELDS);
                        $bonusall = $this->ReturnCurrency($bonus['bonus']);
                     }
                     if ($res->isSuccess()) {
                        Application::getConnection()->commitTransaction();
                     } else {
                        Application::getConnection()->rollbackTransaction();
                     }
                  }
               }
               $FIELDS['BONUS_ALL'] = $bonusall;
               $NOTIFICATIONS['BONUS_ALL'] = $bonusall;
            } else {
               $ball = 0;
               if ($profile['SCOREIN'] == 'Y') {
                  $cur = $this->ReturnCurrency($bonus['bonus']);
                  if ($ar = \CSaleUserAccount::GetByUserID($Settings['USER_ID'], $bonus['CURRENCY'])) {
                     if ($ar)
                        $ball = $this->ReturnCurrency($ar['CURRENT_BUDGET']);
                     else
                        $ball = $this->ReturnCurrency($bonus['bonus']);
                  }
               } else {
                  $resbon = Vbchbbonus\AccountTable::getList(array(
                              'filter' => array('USER_ID' => $Settings['USER_ID'], "BONUSACCOUNTSID" => $ac),
                          ))->fetch();
                  if ($resbon)
                     $ball = $this->ReturnCurrency($resbon['CURRENT_BUDGET']);
                  else
                     $ball = $this->ReturnCurrency($bonus['bonus']);
               }
               $FIELDS['BONUS_ALL'] = $ball;
               $NOTIFICATIONS['BONUS_ALL'] = $ball;
            }
            foreach (GetModuleEvents($this->module_id, "OnAfterBonusAdd", true) as $arEvent)
               ExecuteModuleEventEx($arEvent, array(&$FIELDS));

            $FIELDS['MAIL_TYPE'] = ($bonus['bonus'] < 0) ? 'DELETE' : 'ADD';
            if (array_key_exists('MAIL_TEMP', $Settings))
               $FIELDS['MAIL_TYPE'] = $Settings['MAIL_TEMP'];
            $FIELDS['BONUS'] = $this->ReturnCurrency($FIELDS['BONUS']);
            $FIELDS['repl'] = $repl;
            $this->Notification($FIELDS, $this->CheckSerialize($profile['NOTIFICATION']));
         }
      }
   }

   private function GetFunction($scoreIn = '') {
      if ($scoreIn) {
         return ($scoreIn == "Y" ? "\\ITRound\\Vbchbbonus\\TmpTable" : "\\ITRound\\Vbchbbonus\\BonusTable");
      }
   }

   public function DeleteBonus($EVNTS = "", $fields = array()) {
      foreach (array("ITRound\\Vbchbbonus\\TmpTable", "ITRound\\Vbchbbonus\\BonusTable") as $cls) {
         if ($this->CheckArray($fields)) {
            $k = $fields['IDUNITS'];
            $TYPE = $this->GetProfileTypeFromFunc($EVNTS);
            $res = call_user_func_array(array($cls, "getList"), array(array('filter' => array('>BONUS' => 0, "TYPES" => $TYPE))));
            if (!is_array($fields['IDUNITS']))
               $fields['IDUNITS'] = array($fields['IDUNITS']);
            while ($rs = $res->fetch()) {
               $option = $this->CheckSerialize($rs['OPTIONS']);
               foreach ($k as $ids => $IDUNITS) {
                  if ($fields['TYPE'] != '') {
                     $f[$ids] = $rs['TYPES'] . $option['PROFILE_ID'] . '_' . str_replace($fields['TYPE'], "", $IDUNITS);
                  } else {
                     $f['IDUNITS'][$ids] = $rs['TYPES'] . $option['PROFILE_ID'] . '_' . $IDUNITS;
                  }
               }
               if (in_array($option['IDUNITS'], $f['IDUNITS'])) {
                  $newFlds = $rs;
                  unset($newFlds['ID']);
                  $newFlds['ACTIVE'] = 'Y';
                  $newFlds['ACTIVE_FROM'] = '';
                  $newFlds['ACTIVE_TO'] = '';
                  $bon = $newFlds['BONUS'];
                  $opt = $option;
                  $opt['IDUNITS'] = '';
                  $newFlds['OPTIONS'] = serialize($opt);
                  $newFlds['BONUS'] = $rs['ACTIVE'] == 'N' ? 0 : $bon;
                  call_user_func_array(array($cls, "update"), array($rs['ID'], $newFlds));
                  $profile = Vbchbbonus\CvbchbonusprofilesTable::getList(
                                  array('filter' => array('=ID' => intval($option['PROFILE_ID'])))
                          )->fetch();
                  if ($rs['ACTIVE'] == 'Y') {
                     $bonus = $this->BonusParams(-$bon, $this->CheckSerialize($profile['BONUSCONFIG']));
                     $bonus['bonus'] = $this->GetAllBonus($bonus['bonus'], $bonus['bonus'], true);
                     $this->AddBonus($bonus, array('IDUNITS' => '', 'ORDER_ID' => '', 'USER_ID' => $rs['USERID'], 'SITE_ID' => $this->SITE_ID), $profile);
                  }

                  $this->DelReferalBonus($profile, $fields);

                  /*
                    $settings=$this->CheckSerialize($profile['SETTINGS']);
                    if($this->CheckArray($settings)){
                    if(array_key_exists("REFBONUS",$settings)){
                    if($settings['REFBONUS']['ACTIVE']=='Y'){
                    $val=$settings['REFBONUS']['COUNT'];
                    $bon = $this->BonusParams(-$val, $this->CheckSerialize($profile['BONUSCONFIG']));
                    $refUser=Vbchreferal::GetRefFrom($rs['USERID'],$rs['SITE']);
                    if(intval($refUser['REFFROM'])>0)
                    $this->AddBonus($bon, array('IDUNITS'=>'','USER_ID' =>  $refUser['REFFROM'], 'SITE_ID' => $this->SITE_ID), $profile);
                    }
                    }
                    } */
               }
            }
         }
      }
   }

   public function GetOrderCountByUserPayPeriod($USER_ID, $period = array()) {

      $replc = array("W" => "1 weeks", "2W" => "2 weeks", "M" => "1 months", "3M" => "3 months", "6M" => "6 months", "Y" => "1 years");
      $filter = array();
      $now = new Main\Type\DateTime();
      $l = new Main\Type\DateTime();
      $filter = array(
          "USER_ID" => $USER_ID,
          "PAYED" => "Y",
          "<=DATE_INSERT" => $now,
          ">=DATE_INSERT" => $l->add("- " . $replc[$period['PERIOD']]),
      );

      if ($USER_ID) {
         if ($this->isD7()) {
            $order = \Bitrix\Sale\Order::getList(array(
                        'filter' => $filter,
                    ))->getSelectedRowsCount();
            return $order;
         } else {
            $order = \CSaleOrder::GetList(array('ID' => 'ASC'), $filter)->SelectedRowsCount();
            return $order;
         }
      }
   }

   private function InnerCheck($bonus = 0, $fields = array(), $profile) {
      $res1 = false;
      if (intval($bonus) < 0) {
         return $res1;
      }
      $this->ACCOUNT_BITRIX_CLASS = $this->GetFunction($profile['SCOREIN']);
      if ($this->CheckArray($fields)) {
         $res = call_user_func_array(array($this->ACCOUNT_BITRIX_CLASS, "getList"), array(array('filter' => array('TYPES' => $profile['TYPE']))));
         if ($res) {
            while ($rs = $res->fetch()) {
               $option = $this->CheckSerialize($rs['OPTIONS']);
               if ($option['IDUNITS'] == $fields['IDUNITS'])
                  $res1 = true;
            }
         }
      }
      return $res1;
   }

   public function Notification($FIELDS = array(), $Settings = array()) {
      $FIELDS['DESCRIPTION'] = str_replace($FIELDS['repl'][0], $FIELDS['repl'][1], $Settings['EMAILTEMPLATE']);
      $FIELDS['DESCRIPTION_DEL'] = str_replace($FIELDS['repl'][0], $FIELDS['repl'][1], $Settings['EMAILTEMPLATEDEL']);
      $FIELDS['SMSDESCRIPTION'] = str_replace($FIELDS['repl'][0], $FIELDS['repl'][1], $Settings['SMSMESSAGE']);
      $sendFIELDS = [];
      foreach ($FIELDS['repl'][0] as $key => $id) {
         $sendFIELDS[str_replace("#", "", $id)] = $FIELDS['repl'][1][$key];
      }
      $sendFIELDS['MESSAGE'] = $FIELDS['DESCRIPTION'];
      if ($Settings['SENDEMAIL'] == 'Y') {
         $el = array('FIELDS' => $FIELDS, 'SETTINGS' => $Settings);

         foreach (GetModuleEvents($this->module_id, "OnBeforeBonusNotification", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$el));

         $FIELDS = $el['FIELDS'];
         $evntID = $this->GetOptions($FIELDS["LID"], $this->mail_events_prefix . "_" . $FIELDS["MAIL_TYPE"]);
         $evntID = $evntID['OPTION'];
         $event = new \CEvent();
         $id = $event->Send($this->mail_temp_prefix, $FIELDS["LID"], $sendFIELDS, "N", $evntID);
         unset($event);
         $el = array('FIELDS' => $FIELDS, 'SETTINGS' => $Settings);

         foreach (GetModuleEvents($this->module_id, "OnAfterBonusNotification", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$el));

         $FIELDS = $el['FIELDS'];
      }

      if ($Settings['SENDSMS'] == 'Y') {
         $el = array('FIELDS' => $FIELDS, 'SETTINGS' => $Settings);
         foreach (GetModuleEvents($this->module_id, "OnBonusSendSMS", true) as $arEvent)
            ExecuteModuleEventEx($arEvent, array(&$el));
         $FIELDS = $el['FIELDS'];
      }
   }

   public function ModuleCurrency($bonusinnerid = null) {
      $Currency = "";
      Loader::includeModule('sale');
      $option = $this->GetBonusCurrency($bonusinnerid);
      if ($option["OPTION"]["SUFIX"] == "NAME" && sizeof(array_filter($option["OPTION"]["NAME"])) == 0) {
         $Currency = \Bitrix\Currency\CurrencyManager::getBaseCurrency();
      } elseif ($option["OPTION"]["SUFIX"] == "NAME") {
         $titles[0] = $option["OPTION"]["NAME"][1];
         $titles[1] = $option["OPTION"]["NAME"][2];
         $titles[2] = $option["OPTION"]["NAME"][3];
         $Currency = $titles[0];
      } elseif ($option["OPTION"]["SUFIX"] == "CURRENCY" && Loader::includeModule('catalog')) {
         $Currency = $option["OPTION"]["CURRENCY"];
      } else {
         $Currency = \Bitrix\Currency\CurrencyManager::getBaseCurrency();
      }
      return $Currency;
   }

   public function ReturnCurrency($number, $bonusinnerid = null) {
      Loader::includeModule('catalog');
      Loader::includeModule('currency');
      $Currency = "";
      $option = $this->GetBonusCurrency($bonusinnerid);
      if ($option['OPTION']["SUFIX"] == '' || ($option['OPTION']["SUFIX"] == 'NAME' && sizeof(array_filter($option['OPTION']['NAME']) == 0))) {
         return \CCurrencyLang::CurrencyFormat(round($number, 2), \Bitrix\Currency\CurrencyManager::getBaseCurrency());
      }
      if ($option['OPTION']["SUFIX"] == "NAME") {
         $Currency = $this->GetWordForm(round($number, 2), $option["OPTION"]["NAME"][1], $option["OPTION"]["NAME"][2], $option["OPTION"]["NAME"][3]);
      } elseif ($option['OPTION']["SUFIX"] == "CURRENCY") {
         $Currency = \CCurrencyLang::CurrencyFormat(round($number, 2), $option["OPTION"]["CURRENCY"]);
      }
      return $Currency;
   }

   public function GetBonusCurrency($id = null) {
      $option = array();
      if ($id) {
         $res = CVbchBonusaccountsTable::getList(array(
                     'filter' => array('ACTIVE' => "Y", "ID" => $id),
                 ))->fetchAll();
         if (sizeof($res) > 0) {
            $res = current($res);
            $option['OPTION'] = $this->CheckSerialize($res['SETTINGS']);
         } else {
            $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
         }
      } else {
         $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
      }
      return $option;
   }

   public function SenderGetMalingByEmail($EMail = "") {
      $res = array();
      if (Loader::includeModule('sender')) {
         $r = \Bitrix\Sender\GroupTable::getList(array(
                     'select' => array('*', 'GROUP_CONNECTOR', 'MAILING_GROUP'),
                 ))->fetchAll();
         if ($this->CheckArray($r)) {
            foreach ($r as $r1) {
               foreach (array($r1['SENDER_GROUP_GROUP_CONNECTOR_ENDPOINT']) as $endpoint) {
                  $connector = \Bitrix\Sender\ConnectorManager::getConnector($endpoint);
                  if ($connector) {
                     $connector->setFieldValues($endpoint['FIELDS']);
                     $l = $connector->getData();
                     while ($p = $l->Fetch()) {
                        if ($p['EMAIL'] == $EMail)
                           $res[] = $r1['SENDER_GROUP_MAILING_GROUP_MAILING_ID'];
                     }
                     break;
                  }
               }
            }
         }
         $q = \Bitrix\Sender\MailingSubscriptionTable::getList(array(
                     'select' => array("*", "MAILING", "CONTACT"),
                     'filter' => array("SENDER_MAILING_SUBSCRIPTION_CONTACT_EMAIL" => $EMail),
                 ))->fetchAll();
         if ($this->CheckArray($q)) {
            foreach ($q as $qw) {
               $res[] = $qw['SENDER_MAILING_SUBSCRIPTION_MAILING_ID'];
            }
         }
         $res = array_unique($res);
      }
      return $res;
   }

   public function SenderGetMalingByEmail1($EMail = "") {
      $result = array();
      if (Loader::includeModule('sender')) {
         $subscriptionDb = \Bitrix\Sender\MailingSubscriptionTable::getSubscriptionList(array(
                     'select' => array('EXISTED_MAILING_ID' => 'MAILING.ID'),
                     'filter' => array('=CONTACT.EMAIL' => trim($EMail)),
                 ))->fetchAll();
         foreach ($subscriptionDb as $p)
            $result[] = $p['EXISTED_MAILING_ID'];
      }
      return array_unique($result);
   }

   public function GetCountReview($USERID, $type, $TYPE_ID, $TOPIC = 0) {
      global $DB;
      $create_from = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)), mktime("00", "00", "00", date("m"), date("d"), date("Y")));
      $create_to = date($DB->DateFormatToPHP(\CSite::GetDateFormat("FULL", SITE_ID)), mktime("23", "59", "59", date("m"), date("d"), date("Y")));
      $cnt = 0;
      if ($type == 'FORUM') {
         $cnt = \CForumMessage::GetList(array("ID" => "ASC"),
                         array("FORUM_ID" => $TYPE_ID, "TOPIC_ID" => $TOPIC, "AUTHOR_ID" => $USERID,
                             ">=POST_DATE" => $create_from, "<=POST_DATE" => $create_to))->SelectedRowsCount();
      } elseif ($type == 'BLOG') {
         $filter = array(
             'AUTHOR_ID' => $USERID,
             'BLOG_ID' => $TYPE_ID,
             ">=DATE_CREATE" => $create_from,
             "<=DATE_CREATE" => $create_to,
         );
         $cnt = \CBlogComment::GetList(array(), $filter, false, false, array("ID"))->SelectedRowsCount();
      } elseif ($type == 'IB') {
         $cnt = \CIBlockElement::GetList(array(),
                         array(
                             "IBLOCK_ID" => $TYPE_ID,
                             'CREATED_BY' => $USERID,
                             ">=DATE_CREATE" => $create_from,
                             "<=DATE_CREATE" => $create_to,
                         ), false, false, array('ID', 'DATE_CREATE'))->SelectedRowsCount();
      }
      return $cnt;
   }

   public function GetUnique($txt) {
      return 'Y';
   }

   public function GetCountSocial($social, $user) {
      return Vbchbbonus\CvbchbonussocpushTable::getList(array(
                  'filter' => array('USER_ID' => $user, 'SOCIAL' => $social),
              ))->getSelectedRowsCount();
   }

   public function DelAp($txt) {
      $tmp = substr(trim($txt), -2);
      if (trim($tmp) === "&&") {
         $txt = $this->DelAp(substr(trim($txt), 0, strlen(trim($txt)) - 2));
      } else {
         $txt = substr(trim($txt), 0, strlen(trim($txt)));
      }
      return $this->FDelAp($txt);
   }

   public function FDelAp($txt) {
      $tmp = substr(trim($txt), 0, 2);
      if (trim($tmp) === "&&") {
         $txt = $this->DelAp(substr(trim($txt), 2, strlen(trim($txt))));
      } else {
         $txt = substr(trim($txt), 0, strlen(trim($txt)));
      }
      return $txt;
   }

   public function PrepareIBlock() {
      $arIBlock = array();
      $UseSKU = true;
      $excludeIBlock = array();
      if (!is_array($arIBlock))
         $arIBlock = array();
      $catalogSKU = array();
      if ($UseSKU) {
         foreach ($arIBlock as $iblocID) {
            if ($iblock = \CCatalog::GetByID($iblocID)) {
               if (intval($iblock['OFFERS_IBLOCK_ID']) > 0)
                  $catalogSKU[] = $iblock['OFFERS_IBLOCK_ID'];
            }
         }
      }
      return array_merge($arIBlock, $catalogSKU);
   }

   public function GetFilterString($LEFT = array(), $RIGHT = array(), $replace = array(), $logic = array()) {
      $filter = '';
      $l = false;
      foreach ($LEFT as $FKey => $FValue) {
         if (array_key_exists($FKey, $replace))
            $right = $RIGHT[$replace[$FKey]];
         else
            $right = $RIGHT[$FKey];
         $filter .= " && ";
         if (array_key_exists($FKey, $logic))
            $logc = $logic[$FKey];
         else
            $logc = '==';
         if (is_array($FValue)) {
            if (is_array($right)) {
               $filter .= 'sizeof(array_intersect(array(' . implode(",", $right) . '),array(' . implode(",", $FValue) . ')))>0 ';
            } elseif (is_string($right)) {
               $filter .= 'in_array("' . $right . '",array(' . implode(",", $FValue) . ')) ';
            } elseif (is_int($right) || is_float($right)) {
               $filter .= 'in_array(' . $right . ',array(' . implode(",", $FValue) . ')) ';
            }
         } elseif (is_string($FValue)) {
            if (is_array($right)) {
               $filter .= 'in_array("' . $FValue . '",array(' . implode(",", $right) . ')) ';
            } elseif (is_string($right)) {
               $filter .= '("' . trim($right) . '"' . $logc . '"' . trim($FValue) . '")';
            } elseif (is_int($right) || is_float($right)) {
               $filter .= '("' . $right . '"' . $logc . '"' . trim($FValue) . '")';
            }
         } elseif (is_int($FValue) || is_float($right)) {
            if (is_array($right)) {
               $filter .= 'in_array("' . $FValue . '",array(' . implode(",", $right) . ')) ';
            } elseif (is_string($right)) {
               $filter .= '("' . trim($right) . '"' . $logc . '"' . ($FValue) . '")';
            } elseif (is_int($right) || is_float($right)) {
               $filter .= '(' . $right . $logc . $FValue . ')';
            }
         } elseif (is_bool($FValue) && $right != '') {
            if (is_bool($right)) {
               $filter .= '(' . $right . $logc . $FValue . ')';
            } else {
               $filter .= '(' . $right . $logc . $FValue . ')';
            }
         }
      }
      $filter = $this->DelAp($filter);
      $pp = explode("&&", $filter);
      $pp = array_filter($pp, 'trim');
      $filter = implode("&&", $pp);
      try {
         $l = @eval('return ' . $filter . ';');
      } catch (Main\SystemException $exeption) {
         echo(Loc::getMessage('ITR_VBCH_ERROR_FILTER') . $exeption->getMessage());
      }
      return $l;
   }

   public function RUNBONUS($EVNTS = "", $arFields = array()) {
      global $bonusNum;
      $bonusNum = 0;
      $TYPE = $this->GetProfileTypeFromFunc($EVNTS);
      $res = Vbchbbonus\CvbchbonusprofilesTable::getList(array(
                  'filter' => array('ACTIVE' => 'Y', 'TYPE' => $TYPE, 'SITE' => $this->SITE_ID),
      ));
      while ($prof = $res->fetch()) {
         $this->AddBonTYPE($prof['TYPE'], $prof, $arFields);
      }

      unset($bonusNum);
   }

   private function AddBonTYPE($TYPE, $prof, $arFields1 = array()) {
      global $bonusNum;

      if (\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isAdminSection()) {
         $check = ($prof['ISADMIN'] == 'Y');
      } else
         $check = true;
      if ($check) {
         $Filter = call_user_func_array(array($this->INSTALL_PROFILE[$TYPE], "GetRules"), array($this->FUNC_GETRULES[$TYPE], $prof['ID'], $this->CheckSerialize($prof['FILTER']), $arFields1));
         if ($Filter) {
            $bonus = call_user_func_array(array($this->INSTALL_PROFILE[$TYPE], "GetBonus"), array($this->FUNC_GETBONUS[$TYPE], $prof, $arFields1));
            $bonus = $this->BonusParams($bonus, $this->CheckSerialize($prof['BONUSCONFIG']));
            $bonusNum = floatval($bonusNum) + floatval($bonus['bonus']);
            $this->AddBonus($bonus, array('IDUNITS' => $TYPE . $prof['ID'] . '_' . $arFields1['ID'], 'USER_ID' => $arFields1['USERID'], 'ORDER_ID' => $arFields1['ORDER_ID'], 'SITE_ID' => $this->SITE_ID), $prof);

            $prof_settings = $this->CheckSerialize($prof['SETTINGS']);

            if (array_key_exists("ORDERPROPBONUS", $prof_settings) && array_key_exists('ORDER_ID', $arFields1)) {
               if ($prof_settings['ORDERPROPBONUS']['ACTIVE'] == 'Y') {
                  $arOrder = \Bitrix\Sale\Order::load(intval($arFields1['ORDER_ID']));
                  $propID = $this->ReturnOrderProp($arOrder->getField('PERSON_TYPE_ID'), $prof_settings['ORDERPROPBONUS']['ID']);
                  if ($propID && $bonusNum > 0) {
                     $this->AddOrderProperty($propID, $bonusNum, $arFields1['ORDER_ID']);
                  }
               }
            }
            if (array_key_exists("REFKA", $arFields1)) {
               //add referal bonus
               $this->AddReferalBonus($prof, $arFields1);
               //add referal bonus end
            }
         }
      }
   }

   public function DelReferalBonus($prof, $arFields1) {
      $refActive = $this->GetOptions($this->SITE_ID, 'REFACTIVE');
      $refLINE = $this->GetOptions($this->GetSiteID(), 'REFLEVELCOUNT');

      if ($refActive['OPTION'] == 'Y') {
         $setprof = $this->CheckSerialize($prof['SETTINGS']);
         $bonusRef = array();
         foreach ($setprof['REFBONUS']['ACTIVE'] as $k_id => $l) {
            if ($l == 'Y') {
               $bonusRef[] = array('ID' => $k_id, 'BONUS' => $setprof['REFBONUS']['COUNT'][$k_id], 'TYPE' => $setprof['REFBONUS']['TYPE'][$k_id]);
            }
         }
         if ($this->CheckArray($bonusRef)) {
            $res = $this->GetLineFromUserID($arFields1['USERID'], $this->SITE_ID);
            if ($this->CheckArray($res['PATH'])) {
               foreach ($res['PATH'] as $pp => $refpath) {
                  $bnsref = $this->GetRefBonusCount($bonusRef[$pp], $arFields1['SUM_PAID'], $this->GetBonusFromOrderID($arFields1['ORDER_ID']));
                  $bonus = $this->BonusParams(-$bnsref, $this->CheckSerialize($prof['BONUSCONFIG']));
                  $l = $this->CheckSerialize($prof['BONUSCONFIG']);
                  $l['BONUSINNERIN']['BONUSINNER'] = $setprof['REFACCOUNT']['BONUSINNER'];
                  $prof['BONUSCONFIG'] = base64_encode(serialize($l));
                  $this->AddBonus($bonus, array('IDUNITS' => $prof['TYPE'] . $prof['ID'] . '_' . $refpath['USERID'] . '_lines-' . ($pp + 1) . '_' . $arFields1['REFKA'] . '_' . $arFields1['ORDER_ID'], 'ORDER_ID' => $arFields1['ORDER_ID'], 'USER_ID' => $refpath['USERID'], 'SITE_ID' => $this->SITE_ID), $prof, false, true);
               }
            }
         }
      }
   }

   function GetBudgetFromUserID($USER_ID, $site, $accounts) {
      $dbAccountUser = Vbchbbonus\AccountTable::getList(array(
                  'filter' => array("USER_ID" => $USER_ID, 'BONUSACCOUNTSID' => $accounts),
              ))->fetchAll();
      $l = array();
      if ($this->CheckArray($dbAccountUser)) {
         foreach ($dbAccountUser as $pp) {
            $l[$pp['BONUSACCOUNTSID']] = floatval($pp['CURRENT_BUDGET']);
         }
      }
      return $l;
   }

   public function AddReferalBonus($prof, $arFields1) {
      if (array_key_exists("REFKA", $arFields1) && $arFields1['REFKA'] !== '') {
         $refActive = $this->GetOptions($this->SITE_ID, 'REFACTIVE');
         $refLINE = $this->GetOptions($this->GetSiteID(), 'REFLEVELCOUNT');
         $Filter = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($this->FUNC_GETRULES[$prof['TYPE']], $prof['ID'], $this->CheckSerialize($prof['FILTER']), $arFields1));
         if ($refActive['OPTION'] == 'Y' && $Filter) {
            $setprof = $this->CheckSerialize($prof['SETTINGS']);
            $BONUSCONST = $setprof['BONUSCONTS'];
            $bonusRef = array();
            foreach ($setprof['REFBONUS']['ACTIVE'] as $k_id => $l) {
               if ($l == 'Y') {
                  $bonusRef[] = array('ID' => $k_id, 'BONUS' => $setprof['REFBONUS']['COUNT'][$k_id], 'TYPE' => $setprof['REFBONUS']['TYPE'][$k_id]);
               }
            }
            $order_price = $setprof['WITHOUTDELIVERYPRICE'] == 'Y' ? $arFields1['ORDER_PRICE'] : $arFields1['SUM_PAID'];

            if ($this->CheckArray($bonusRef) || $BONUSCONST) {
               $res = $this->GetLineFromUserID($arFields1['REFKA'] ? $arFields1['REFKA'] : $arFields1['USERID'], $this->SITE_ID);
               if ($this->CheckArray($res['PATH'])) {
                  foreach ($res['PATH'] as $pp => $refpath) {
                     if ($bonusRef[$pp])
                        $bnsref = $this->GetRefBonusCount($bonusRef[$pp], $arFields1['BALL_ORDER'] ? $arFields1['BALL_ORDER'] : $order_price, $this->GetBonusFromOrderID($arFields1['ORDER_ID']));
                     else {
                        $bnsref = 0;
                     }
                     $bonus = $this->BonusParams($bnsref + $BONUSCONST, $this->CheckSerialize($prof['BONUSCONFIG']));
                     $l = $this->CheckSerialize($prof['BONUSCONFIG']);
                     $l['BONUSINNERIN']['BONUSINNER'] = $setprof['REFACCOUNT']['BONUSINNER'];
                     $prof['BONUSCONFIG'] = base64_encode(serialize($l));
                     $this->AddBonus($bonus, array('IDUNITS' => $prof['TYPE'] . $prof['ID'] . '_' . $refpath['USERID'] . '_lines-' . ($pp + 1) . '_' . $arFields1['REFKA'] . '_' . $arFields1['ORDER_ID'], 'ORDER_ID' => $arFields1['ORDER_ID'], 'USER_ID' => $refpath['USERID'], 'SITE_ID' => $this->SITE_ID), $prof, false, true);
                  }
               }
            }
         }
      }
   }

   function GetRefBonusCount($settings, $ordersumm = 0, $userbonus = 0) {
      switch ($settings['TYPE']) {
         case 'ORDERSUMM':
            return ($this->GetAllBonus($settings['BONUS'], $ordersumm));
            break;
         case 'BONUS':
            return ($this->GetAllBonus($settings['BONUS'], $userbonus));
            break;
         case 'FIXPRICE':
            return floatval(str_replace("%", "", $settings['BONUS']));
            break;
         default:
            break;
      }
   }

   function GetUserStatus($partner_group_id, $USERID, $f) {
      $lp = array();
      $profiles = Vbchbbonus\CvbchbonusprofilesTable::getList(
                      array(
                          'filter' => array('ACTIVE' => "Y", "!TYPE" => array("BONUS", "BONUSPAY", "FIRSTORDER"), "SITE" => $this->SITE_ID),
                      )
              )->fetchAll();

      $user_group = \CUser::GetUserGroup($USERID);
      if ($this->CheckArray($profiles)) {
         foreach ($profiles as $prof) {
            $filter = $this->CheckSerialize($prof['FILTER']);

            if (sizeof(array_intersect($partner_group_id, $user_group)) > 0) {
               $partner = true;
               if (sizeof(array_intersect($partner_group_id, $filter['USERGROUP'])) > 0) {
                  $checkProfile[] = array('NAME' => $prof['NAME'], $filter['MINBONUSPAY1']);
               }
            } else {
               $partner = false;
               if (sizeof(array_intersect($partner_group_id, $filter['USERGROUP'])) <= 0) {
                  $checkProfile[] = array('NAME' => $prof['NAME'], $filter['ORDERPERIOD']);
               }
            }
         }
      }

      if ($this->CheckArray($checkProfile) && $this->CheckArray($f)) {
         foreach ($checkProfile as $chprof) {
            if ($partner) {
               $str = 'return (';
               foreach ($chprof[0] as $id_b => $pf) {
                  if ($pf[0] == '')
                     $pf[0] = 0;
                  if ($pf[1] == '')
                     $pf[1] = 0;
                  if (!array_key_exists($id_b, $f))
                     $f[$id_b] = 0;
//							if($pf[0]!='' && $pf[1]!='' && $f[$id_b]!=''){
                  $str .= '(' . $f[$id_b] . '>=' . $pf[0] . ' && ' . $f[$id_b] . '<=' . $pf[1] . ')';
                  $str .= " && ";
                  //						}//else return $checkProfile[0]['NAME'];
               }
               $str .= "1);";

               if ($str != 'return (1);' && eval($str)) {
                  return $chprof['NAME'];
               }
            } else {
               if ($chprof[0]['ACTIVE'] == 'Y') {
                  $USER_ORDER_SUMM = $this->GetUserOrderSumm($USERID,
                          array('SUMMA' => $chprof[0]['SUMMA'],
                              'SUMMA1' => $chprof[0]['SUMMA1'],
                              'PERIOD' => $chprof[0]['PERIOD'],
                              'STATUS' => $chprof[0]['STATUS'],
                              "ORDER_ID" => '')
                  );
                  if ($USER_ORDER_SUMM)
                     return $chprof['NAME'];
               }
            }
         }
      } else {
         return $checkProfile[0]['NAME'];
      }
   }

   function GetLineFromUserIDBase($USER_ID, $base, &$line = 0, $path = array()) {
      foreach ($base as $idb => $referal) {
         if ($referal['USERID'] == $USER_ID) {
            if ($referal['REFFROM'] == 0) {
               $line = $line;
               $ref = $referal['USERID'];
            } elseif ($referal['REFFROM'] > 0) {
               $path[$line] = array('USERID' => $referal['REFFROM']);
               $line = $line + 1;
               $l = $this->GetLineFromUserIDBase($referal['REFFROM'], $base, $line, $path);

               $line = $l['line'];
               $path = $l['PATH'];
               $ref = $l['REF'];
            }
         }
      }
      return array('line' => $line, 'REF' => $ref, 'PATH' => $path);
   }

   function GetLineFromUserID($USER_ID, $site, &$line = 0, $path = array()) {
      $base = CVbchRefTable::getList(array(
                  'filter' => array('LID' => $site, 'USERID' => $USER_ID),
                  'select' => array('USERID', 'REFFROM')
      ));
      while ($referal = $base->fetch()) {
         if ($referal['USERID'] == $USER_ID) {
            if ($referal['REFFROM'] == 0) {
               //$line=0;
               $ref = $referal['USERID'];
               //$path[$line]=array('USERID'=>$referal['REFFROM']);
            } elseif ($referal['REFFROM'] > 0) {
               $path[$line] = array('USERID' => $referal['REFFROM']);
               $line = $line + 1;
               $l = $this->GetLineFromUserID($referal['REFFROM'], $site, $line, $path);

               $line = $l['line'];
               $path = $l['PATH'];
               $ref = $l['REF'];
            }
         }
      }

      return array('line' => $line, 'REF' => $ref, 'PATH' => $path);
   }

   function F1(&$newBase) {
      foreach ($newBase as &$nb) {
         $chk = array();
         $chk = $this->GetChildCound($nb['USERID'], $newBase);
         $nb['CHILD'] = $chk;
         $pp = $this->GetLineFromUserIDBase($nb['USERID'], $newBase);
         $nb['DEPTH_LEVEL'] = $pp['line'] + 1;
         $nb['IS_PARENT'] = (sizeof($chk) > 0 ? true : false);
         $nb['INFO'] = $pp;
         unset($nb);
      }
   }

   function GetChildCound($ID, $base) {
      $child = array();
      foreach ($base as $ll) {
         if ($ll['REFFROM'] == $ID) {
            $child[] = $ll;
         }
      }
      return $child;
   }

   public function UserActive($USERID) {
      $filter = array("USERID" => $USERID);
      $res = \Bitrix\Main\UserTable::getList(array(
                  'filter' => array("ID" => $USERID),
                  'select' => array("ACTIVE"),
                  'cache' => array(
                      'ttl' => 60,
                      'cache_joins' => true,
                  )
              ))->fetch();

      return $res['ACTIVE'];
   }

   public function GetUserGroupByUser($USER_ID) {
      $result = array();
      $res = \Bitrix\Main\UserGroupTable::getList(
                      array(
                          'filter' => array('USER_ID' => $USER_ID),
                          'select' => array("GROUP_ID"),
                          'cache' => array(
                              'ttl' => 60,
                              'cache_joins' => true,
                          )
                      )
      );
      while ($r = $res->fetch()) {
         $result[] = $r['GROUP_ID'];
      }
      $result[] = '2'; //support user group ALL USERS (2)
      asort($result);
      return $result;
   }

   public function GetPartPayOrder($order_id, $user_id, $score_in) {
      $order_id = intval($order_id);
      $user_id = intval($user_id);
      if ($order_id == 0) {
         return false;
      } else {
         $IDUNITS = 'PAY_PART_' . $order_id;
         $this->ACCOUNT_BITRIX_CLASS = $this->GetFunction($score_in);
         $res = \ITRound\Vbchbbonus\BonusTable::getList(array(
                     'filter' => array('TYPES' => 'BONUSPAY', 'USERID' => $user_id),
                     'cache' => array(
                         'ttl' => 60,
                         'cache_joins' => true,
                     )
         ));
         while ($rs = $res->fetch()) {
            $option = $this->CheckSerialize($rs['OPTIONS']);
            if ($option['IDUNITS'] == $IDUNITS)
               return true;
         }
      }
   }

   public function GetUserOrderSumm($USER_ID, $PERIOD = array()) {

      $replc = array("W" => "1 weeks", "2W" => "2 weeks", "M" => "1 months", "3M" => "3 months", "6M" => "6 months", "Y" => "1 years");
      $filter = array();
      $now = new Main\Type\DateTime();
      $l = new Main\Type\DateTime();
      $summ = 0;
      $filter = array(
          "USER_ID" => $USER_ID,
          "STATUS_ID" => $PERIOD['STATUS'],
          "<=DATE_INSERT" => $now,
          ">=DATE_INSERT" => $l->add("- " . $replc[$PERIOD['PERIOD']]),
      );
      if ($PERIOD['PERIOD'] == 'ALL') {
         unset($filter['<=DATE_INSERT'], $filter['>=DATE_INSERT']);
      }
      if ($PERIOD['ORDER_ID'] || $PERIOD['ORDER_ID'] != 0) {
         $filter['!ID'] = $PERIOD['ORDER_ID'];
      }
      if ($this->isD7()) {
         $dbOrderList = \Bitrix\Sale\Internals\OrderTable::getList(array(
                     'filter' => $filter,
                     'cache' => array(
                         'ttl' => 60,
                         'cache_joins' => true,
                     )
                         )
         );
         while ($ord = $dbOrderList->fetch()) {
            $summ += $ord['PRICE'];
         }
      } else {
         $dbOrderList = \CSaleOrder::GetList(array(), $filter);
         if ($dbOrderList) {
            while ($ord = $dbOrderList->Fetch()) {
               $summ += $ord['PRICE'];
            }
         }
      }
      return ($summ >= $PERIOD['SUMMA'] && $summ <= $PERIOD['SUMMA1']);
   }

   public function GetElementFilter($ELEMENTFilter) {
      $obCond = new Vbchbbonus\CITRBBFilterCatalogCondTree();
      $boolsCond = $obCond->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, array());
      if ($boolsCond)
         return $obCond->Generate($ELEMENTFilter, array('FIELD' => '$arItems'));
   }

   public function isFirstOrder($USER_ID, $order_id = null) {
      $payed = $this->GetOptions($this->SITE_ID, "CHECKPAYORDER");
      $order_status = $this->GetOptions($this->SITE_ID, "CHECKORDERSTATUS");
      $payed = $payed['OPTION'];
      if ($payed == '')
         $payed = 'N';
      $order_status = $order_status['OPTION'];
      if ($order_status == '')
         $order_status = 'F';

      $filter = [
          'USER_ID' => intval($USER_ID),
          'STATUS_ID' => $order_status          
      ];
      if($payed == 'Y')
         $filter['PAYED'] = $payed;

      if ($order_id !== false && $order_id !== 0) {
         $filter['!ID'] = intval($order_id);
      }

      if ($this->isD7()) {
         $dbOrderList = \Bitrix\Sale\Internals\OrderTable::getList(array(
                     'filter' => $filter,
                         )
                 )->getSelectedRowsCount();
      } else {
         $dbOrderList = \CSaleOrder::GetList(array(), $filter)->SelectedRowsCount();
      }
      return ($dbOrderList < 1);
   }

   public function GetRangeBonus($bns) {
      if ($this->CheckArray($bns)) {
         $bns = array_filter($bns);
         if (sizeof($bns) > 1) {
            $option1 = $this->GetOptions($this->SITE_ID, 'BNSELEMENTCART');
            $option1 = $option1['OPTION'];
            if ($option1 == 'SUMM') {
               rsort($bns);
               $bonus = floatval(array_sum($bns));
            } elseif ($option1 == 'MIN') {
               $bns = array_filter($bns);
               asort($bns);
               $bonus = current($bns);
            } elseif ($option1 == 'MAX') {
               $bns = array_filter($bns);
               arsort($bns);
               $bonus = current($bns);
            } else {
               return current($bns);
            }
         } else {
            $bonus = current($bns);
         }
      } else
         $bonus = 0;
      $bonus = $bonus < 0 ? 0 : $bonus;
      return $bonus;
   }

   private function DelZerro($k, $v) {
      if ($v != 0)
         return $k;
   }

   public function GetSettingsProf($ID) {
      $tmp = $this->GetProfiles(array('=ID' => $ID));
      $tmp = current($tmp);
      $k = $this->CheckSerialize($tmp['SETTINGS']);
      $k['SCOREIN'] = $tmp['SCOREIN'];
      return $k;
   }

   public function GetConfigsProf($ID) {
      $tmp = $this->GetProfiles(array('=ID' => $ID));
      $tmp = current($tmp);
      $k = $this->CheckSerialize($tmp['BONUSCONFIG']);
      return $k;
   }

   private function GetProfiles($filter = array()) {
      $profiles = CvbchbonusprofilesTable::getList(array(
                  'filter' => $filter,
              ))->fetchAll();
      return $profiles;
   }

   public function GetProductDiscount($ID, $Catalog_group) {
      if ($ID) {
         $currentDatetime = new Main\Type\DateTime();
         $arDiscounts = array();
         if (Option::get('sale', 'use_sale_discount_only') == 'Y') {
            $arDiscounts = \Bitrix\Sale\Internals\DiscountTable::getList(
                            [
                                'filter' => [
                                    'ACTIVE' => "Y",
                                    'LID' => $this->SITE_ID,
                                    [
                                        'LOGIC' => 'OR',
                                        'ACTIVE_FROM' => '',
                                        '<=ACTIVE_FROM' => $currentDatetime
                                    ],
                                    [
                                        'LOGIC' => 'OR',
                                        'ACTIVE_TO' => '',
                                        '>=ACTIVE_TO' => $currentDatetime
                                    ]
                                ],
                                'cache' => [
                                    'ttl' => 60,
                                    'cache_joins' => true
                                ]
                            ]
                    )->fetchAll();
         } else {
            $arDiscounts = \CCatalogDiscount::GetDiscountByProduct(
                            $ID,
                            $this->arUSER['GROUP'],
                            "N",
                            $Catalog_group,
                            $this->SITE_ID
            );
         }
         return $arDiscounts;
      }
   }

   public function BonusFromProp($Element = array(), $props = array()) {
      $value = '';

      if ($this->CheckArray($Element)) {
         if ($this->CheckArray($props)) {
            foreach ($props as $prp) {
               if ($Element['PROPERTY_' . $prp . '_VALUE']) {
                  $value = $Element['PROPERTY_' . $prp . '_VALUE'];
               } else {
                  $elmnt = \CIBlockElement::GetProperty($Element['IBLOCK_ID'], $Element['ID'], array("sort" => "asc"), Array("ID" => $prp))->Fetch();
                  $value = $elmnt['PROPERTY_TYPE'] == 'L' ? $elmnt['VALUE_ENUM'] : $elmnt['VALUE'];
                  if ($value == '' || $value == 0) {
                     $tmp = \CCatalogSKU::getProductList($Element['PRODUCT_ID']);
                     if ($tmp) {
                        $tmp = $tmp[$Element['PRODUCT_ID']];
                        $elmnt = \CIBlockElement::GetProperty($tmp['IBLOCK_ID'], $tmp['ID'], array("sort" => "asc"), Array("ID" => $prp))->Fetch();
                        if ($elmnt['VALUE'])
                           $value = $elmnt['PROPERTY_TYPE'] == 'L' ? $elmnt['VALUE_ENUM'] : $elmnt['VALUE'];
                     }
                  }
               }
            }
         }
      }
      if (is_array($value) && sizeof($value) == 1)
         $value = current($value);
      return $value;
   }

   public function GetSumPaid($ORDER_ID, $PAYMENT_ID = 'bonus') {
      $paid = 0;
      if ($this->isD7()) {
         $order = \Bitrix\Sale\Order::load($ORDER_ID);
         $payment = $order->getPaymentCollection()->getItemById($PAYMENT_ID);
         $paid = $payment->getSum();
         unset($payment, $order);
      } else {
         $order = \CSaleOrder::GetByID($ORDER_ID);
         $paid = $order['PAID'];
      }

      return $paid;
   }

   public function Raspred($bonuspay, &$trans) {
      if ($bonuspay > 0) {
         foreach ($trans as $id => &$tr) {
            if ($tr['ACTIVE'] == 'Y' && floatval($tr['BONUS']) > 0) {
               $p = floatval($tr['BONUS'] - $tr['PARTPAY']);
               if (floatval($bonuspay) >= $p) {
                  $l = $bonuspay - $p;
                  $tr['ACTIVE'] = 'N';
                  $tr['PARTPAY'] = $tr['BONUS'];
                  $bonuspay = $l;
               } else {
                  $tr['PARTPAY'] += $bonuspay;
                  break;
               }
            }
            unset($tr);
         }
      }
   }

   private static function getDeltaBonusPay($item_bonuspay, $input_bonuspay) {
      $newItemBonuspay = [];
      $percent_delta = 0;
      $summBonuspay = array_sum($item_bonuspay);
      if ($summBonuspay != 0) {
         $percent_delta = round(100 - (($input_bonuspay * 100) / $summBonuspay), 2);
      }
      foreach ($item_bonuspay as $ind => $item_sum) {
         $newItemBonuspay[$ind] = round($item_sum - ($item_sum * $percent_delta) / 100, 2);
      }
      return $newItemBonuspay;
   }

   public function getMaxBonusPay(Sale\Order $order = null, Main\HttpRequest $request = null) {
      $arFields = $this->GetArrayForProfile(0, array(), 1, true, ($order ? $order->getField("ID") : $this->SITE_ID));
      $payProfile = $this->GetBonusPayProfile($arFields);
      return $this->getMinMaxpay($payProfile, $arFields, $request);
   }

   private function getMinMaxpay($payProfile, $arFields, Main\HttpRequest $request) {
      if (Vbchbbcore::CheckArray($payProfile)) {
         $pay = [];
         foreach ($payProfile as $prof) {
            $filter[$prof['ID']] = Vbchbbcore::CheckSerialize($prof['FILTER']);
            $settings = Vbchbbcore::CheckSerialize($prof['SETTINGS']);
            $bonusconfig = Vbchbbcore::CheckSerialize($prof['BONUSCONFIG']);

            $maxPay[$prof['ID']] = roundEx(call_user_func_array(
                            array(
                                $this->INSTALL_PROFILE[$prof['TYPE']],
                                "GetBonus"
                            ),
                            array(
                                $this->FUNC_GETBONUS[$prof['TYPE']],
                                $prof,
                                $arFields
                            )
                    ), $bonusconfig['PRECISION']
            );
            $maxPay[$prof['ID']] = self::Bonusis($bonusconfig, $maxPay[$prof['ID']], 'off');
            //use bitrix user inner account
            if ($prof['SCOREIN'] == 'Y' && $settings['SALEORDERAJAX'] == 'SYSTEMPAY') {
               if ($request->get('PAY_CURRENT_ACCOUNT') == 'Y') {
                  $userInput = roundEx($request->get('ACCOUNT_CNT'), $bonusconfig['PRECISION']);
                  $dbUserAccount = \CSaleUserAccount::GetList(
                                  array(),
                                  array(
                                      'USER_ID' => $arFields['ORDER']['USER_ID'],
                                      'CURRENCY' => $arFields['ORDER']['CURRENCY']
                                  )
                  );
                  if ($arUserAccount = $dbUserAccount->GetNext()) {
                     $currentBudget = roundEx($arUserAccount['CURRENT_BUDGET'], $bonusconfig['PRECISION']);
                     if ($maxPay[$prof['ID']] >= $currentBudget)
                        $maxPay[$prof['ID']] = $currentBudget;
                     if ($userInput >= $maxPay[$prof['ID']])
                        $userInput = $maxPay[$prof['ID']];
                     if ($userInput < $settings['MINIMALBONUSPAY'])
                        $userInput = $settings['MINIMALBONUSPAY'];
                     $userInput = Vbchbbcore::Bonusis($bonusconfig, $userInput, "off");
                  } else
                     $userInput = 0;
                  $pay[$prof['ID']] = $userInput;
               }
            }
            //use module user bonus account
            if ($prof['SCOREIN'] != 'Y' && $settings['SALEORDERAJAX'] == 'BONUSPAY') {
               if ($request->get('PAY_BONUS_ACCOUNT') == 'Y') {
                  $userInput = roundEx($request->get('BONUS_CNT'), $bonusconfig['PRECISION']);
                  $ac = $bonusconfig['BONUSINNERIN']['BONUSINNER'] ? $bonusconfig['BONUSINNERIN']['BONUSINNER'] : $bonusconfig['BONUSINNEROUT']['BONUSINNER'];
                  if ($ac == 0 || $ac == '') {
                     $ac = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
                     $f = Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                                 'filter' => array('PAYSYSTEMID' => $ac),
                                 'select' => array('ID'),
                             ))->fetch();
                     $ac = $f['ID'];
                  }
                  $dbUserAccount = AccountTable::getList(
                                  array(
                                      'filter' => array(
                                          'USER_ID' => $arFields['ORDER']['USER_ID'],
                                          'BONUSACCOUNTSID' => $ac
                                      ),
                                  )
                          )->fetch();
                  $currentBonusBudget = roundEx($dbUserAccount["CURRENT_BUDGET"], $bonusconfig['PRECISION']);
                  if ($maxPay[$prof['ID']] >= $currentBonusBudget)
                     $maxPay[$prof['ID']] = $currentBonusBudget;
                  if ($userInput >= $maxPay[$prof['ID']])
                     $userInput = $maxPay[$prof['ID']];
                  if ($userInput < $settings['MINIMALBONUSPAY'])
                     $userInput = $settings['MINIMALBONUSPAY'];

                  $userInput = Vbchbbcore::Bonusis($bonusconfig, $userInput, "off");
                  $pay[$prof['ID']] = $userInput;
               }
            }
         }
         return ['PAY' => $pay, 'MAXPAY' => $maxPay];
      }
   }

   public function OnSaleComponentOrderCreated(\Bitrix\Sale\Order $order, &$arUserResult, \Bitrix\Main\HttpRequest $request, &$arParams, &$arResult) {
      foreach (GetModuleEvents($this->module_id, "OnBonusPrologOrderCreated", true) as $arEvent)
         ExecuteModuleEventEx($arEvent, array($order, &$arUserResult, $request, &$arParams, &$arResult, $this));

      $this->SITE_ID = SITE_ID;
      global $USER;
      $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
      $option_sms = $this->GetOptions($this->SITE_ID, "CHECKSMSBONUSPAY")['OPTION'] == 'Y';
      $arResult['JS_DATA']['CHECKSMS'] = ($request->get('CHECK_SMS') == 'Y');
      $arResult['JS_DATA']['PAY_BONUS_ACCOUNT'] = ($request->get('PAY_BONUS_ACCOUNT') == 'Y');
      $arResult['JS_DATA']['CHECKSMS'] = ($request->get('CHECK_SMS') == 'Y');
      $smsCODE = trim(htmlspecialchars($request->get('SMSCODE')));
      $arResult['JS_DATA']['SMSCODE'] = $smsCODE;
      $arResult['SMSCODE'] = $smsCODE;

      if ($option_sms) {

         $arResult['REK'] = false;
         if ($USER->isAuthorized()) {
            $arUser = \Bitrix\Main\UserTable::getList(
                            [
                                'filter' => ['=ID' => $USER->GetID()],
                                'select' => ['ID', 'PERSONAL_PHONE'],
                            ]
                    )->Fetch();
         } else
            $arUser = false;
         if ($smsCODE != '' && $arResult['JS_DATA']['CHECKSMS'] && !$arResult['REK']) {
            $verifyCode = false;
            if ($arUser) {
               foreach (GetModuleEvents($this->module_id, "OnBonusVerifySMSCode", true) as $arEvent)
                  ExecuteModuleEventEx($arEvent, array($smsCODE, $arUser, &$verifyCode));

               if ($verifyCode) {
                  $arResult['JS_DATA']['CHECKSMS'] = true;
               } else {
                  $arResult['JS_DATA']['CHECKSMS'] = false;
                  $arResult['JS_DATA']['PAY_BONUS_ACCOUNT'] = false;
               }
            }
         }
         if (!$arResult['JS_DATA']['CHECKSMS'] && $request->get('PAY_BONUS_ACCOUNT') == 'Y' && $smsCODE == '' && !$arResult['JS_DATA']['CHECKSMS'] && $arUser) {
            if ($arResult['JS_DATA']['PAY_BONUS_ACCOUNT']) {
               $bns = intval(htmlspecialchars($request->get('BONUS_CNT')));
               $smsResult = false;
               if ($arUser && $bns) {
                  $Information = [
                      'AMOUNT' => $order->getPrice() + $order->getDeliveryPrice(),
                      'BONUSOFF' => $bns,
                  ];
                  foreach (GetModuleEvents($this->module_id, "OnBonusGetSMSCode", true) as $arEvent)
                     ExecuteModuleEventEx($arEvent, array($arUser, &$Information, &$smsResult));
                  if ($smsResult) {
                     $arResult['SENDSMSCODE'] = true;
                  } else {
                     $arResult['SENDSMSCODE'] = false;
                     $GLOBALS['APPLICATION']->ThrowException(Loc::getMessage('VBCHBB_DONTSEND_SMS'));
                     return false;
                  }
               }
            }
         }
      } else {
         $arResult['JS_DATA']['CHECKSMS'] = true;
      }

      $arFields = $this->GetArrayForProfile(0, array(), 1, true, 0);
      $arFields['DELIVERY_PRICE'] = $arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE'];
      //$arFields['ORDER_PRICE']=floatval($arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE'])- floatval($arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE']);
      $arFields['DELIVERY_ID'] = $this->GetIDChecked($arResult['JS_DATA']['DELIVERY']);
      $arFields['PAY_SYSTEM_ID'] = $this->GetIDChecked($arResult['JS_DATA']['PAY_SYSTEM']);
      $arFields['PERSON_TYPE_ID'] = $this->GetIDChecked($arResult['JS_DATA']['PERSON_TYPE']);

      $bonus_show_title = (Main\Config\Option::get($this->module_id, 'INTEGRATIONORDERBONUSTITLE', "", $this->SITE_ID));
      $bonuspay_show_title = (Main\Config\Option::get($this->module_id, 'INTEGRATIONORDERBONUSPAYTITLE', "", $this->SITE_ID));
      $pf = 0;
      if ($this->CheckSiteOn() && $arResult['JS_DATA']['CHECKSMS']) {
         $PAYPROF = $this->GetBonusPayProfile($arFields);
         if ($this->CheckArray($PAYPROF)) {
            foreach ($PAYPROF as $prof) {
               $filter[$prof['ID']] = $this->CheckSerialize($prof['FILTER']);
               $settings = $this->CheckSerialize($prof['SETTINGS']);
               $bonusconfig = $this->CheckSerialize($prof['BONUSCONFIG']);
               $MAXPAY = intval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
               if ($prof['SCOREIN'] == 'Y' && $settings['SALEORDERAJAX'] == 'SYSTEMPAY' && $settings['BONUSFORDISCOUNT'] == 'Y') {
                  if ($request->get('PAY_CURRENT_ACCOUNT') == 'Y') {
                     $userInput = floatval($request->get('ACCOUNT_CNT'));
                     $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $USER->GetID(), 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
                     if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $currentBudget = round($arUserAccount['CURRENT_BUDGET'], 2);
                     }
                     if ($MAXPAY >= $currentBudget)
                        $MAXPAY = $currentBudget;
                     if ($userInput >= $MAXPAY)
                        $userInput = $MAXPAY;
                     $userInput = $this->Bonusis($bonusconfig, $userInput, "off");
                     $pf = $userInput;
                  }
               }
               if ($prof['SCOREIN'] != 'Y' && $settings['SALEORDERAJAX'] == 'BONUSPAY' && $settings['BONUSFORDISCOUNT'] == 'Y') {
                  if ($request->get('PAY_BONUS_ACCOUNT') == 'Y') {
                     $inputUserB = intval($request->get('BONUS_CNT'));
                     $dbUserAccount = AccountTable::getList(array(
                                 'filter' => array('USER_ID' => $USER->GetID(), 'BONUSACCOUNTSID' => $bonusconfig['BONUSINNEROUT']['BONUSINNER']),
                             ))->fetch();
                     $currentBonusBudget = round($dbUserAccount["CURRENT_BUDGET"], 2);
                     if ($MAXPAY >= $currentBonusBudget)
                        $MAXPAY = $currentBonusBudget;
                     if ($inputUserB >= $MAXPAY)
                        $inputUserB = $MAXPAY;
                     $inputUserB = $this->Bonusis($bonusconfig, $inputUserB, "off");
                     $pf = $inputUserB;
                  }
               }
               $fk[$prof['ID']] = $pf;
            }
         }

         foreach ($order->getBasket() as $productID => $basketItem) {
            $item = $basketItem->getFields();
            $arItem = $item->getValues();
            $MIN_PRICE = [
                'ID' => $arItem['PRODUCT_PRICE_ID'],
                'CAN_BUY' => "Y",
                'DISCOUNT_VALUE' => $arItem['DISCOUNT_VALUE'],
                'PRICE' => $arItem['PRICE'],
                'CURRENCY' => $arItem['CURRENCY'],
                'VATRATE_VALUE' => $arItem['VAT_RATE'],
                'PRICE_TYPE_ID' => $arItem['PRICE_TYPE_ID'],
                'PRICE_ID' => $arItem['PRODUCT_PRICE_ID'],
            ];
            $bnsID = $this->GetBonusElements($arItem['PRODUCT_ID'], $arItem['IBLOCK_ID'], $arItem['QUANTITY'], $MIN_PRICE);
            $arFields['BASKET'][$arItem['PRODUCT_ID']]['BONUS'] = $bnsID;
            $arResult['ELEMENT_BONUS_DATA'][$arItem['ID']] = [
                2 => $this->ReturnCurrency($bnsID)
            ];
            if (\Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERBONUSSHOW', "", $this->SITE_ID) == 'Y') {
               $propertyCollection = $basketItem->getPropertyCollection();
               $propertyItem = $propertyCollection->createItem();
               $result = $propertyItem->setFields(array('NAME' => $bonus_show_title, 'CODE' => 'BONUS', 'VALUE' => $this->ReturnCurrency($bnsID), 'SORT' => 100));
            }
         }
         if ($pf > 0) {
            $arResult['BONUS']['PAYBONUS'] = $pf;
            $basket = $order->getBasket();
            $order_cart_sum = $basket->getPrice();
            $newDiscountAll = 0;
            $sum = 0;
            $OldValuesOffers = [];
            $newPropBonusPay = [];
            if (floatval($pf) > 0) {
               $arFields['BONUSPAY'] = $pf;
               if ($settings['MAXPAYPROP']['ACTIVE'] == 'Y') {
                  $newPropBonusPay = self::getDeltaBonusPay(self::$FromPropBonusPay, $pf);
               }
               $arResult['BonusPayResult'] = [];
               foreach (GetModuleEvents($this->module_id, "OnBonusPrologOrderMiddle", true) as $arEvent)
                  ExecuteModuleEventEx($arEvent, array($order, &$arUserResult, $request, &$arParams, &$arResult, $this));

               foreach ($order->getShipmentCollection() as $shipment) {
                  if (!$shipment->isSystem()) {
                     $shipPrice = $shipment->getPrice();
                     foreach ($basket as $productID => $basketItem) {
                        $item = $basketItem->getFields();
                        $arItem = $item->getValues();
                        $item_filter[$arItem['PRODUCT_ID']] = true;
                        $item_disc[$arItem['PRODUCT_ID']] = true;
                        foreach ($filter as $profile_id => $fltr) {
                           if ($fltr['ELMNTFLTR'] != '((1 == 1))') {
                              $arItems = $arFields['BASKET'][$arItem['PRODUCT_ID']];
                              if (!eval('return ' . $fltr['ELMNTFLTR'] . ';')) {
                                 $item_filter[$arItem['ID']] = false;
                              }
                           }
                           if ($fltr['DISCOUNTWITHOUT']['ACTIVE'] == 'Y') {
                              $discount = $fltr['DISCOUNTWITHOUT']['DISCOUNT'];
                              if ($this->CheckArray($arFields['BASKET'][$arItem['PRODUCT_ID']]['DISCOUNT'])) {
                                 foreach ($arFields['BASKET'][$arItem['PRODUCT_ID']]['DISCOUNT'] as $dsk) {
                                    if ($dsk['ACTIVE'] && in_array($dsk['ID'], $discount)) {
                                       $item_disc[$arItem['PRODUCT_ID']] = false;
                                    }
                                 }
                              }
                           }
                           if (!$item_filter[$arItem['PRODUCT_ID']] || !$item_disc[$arItem['PRODUCT_ID']]) {
                              $order_cart_sum = $order_cart_sum - $arItem['PRICE'];
                           }
                        }
                        if ($item_filter[$arItem['PRODUCT_ID']] && $item_disc[$arItem['PRODUCT_ID']]) {
                           if (sizeof($newPropBonusPay) > 0) {
                              $percent = ($newPropBonusPay[$arItem['PRODUCT_ID']] * 100) / ($arItem["PRICE"] * $arItem["QUANTITY"]);
                              $discountPlus = roundEx((($arItem["PRICE"] * $arItem["QUANTITY"] * $percent) / 100), 1);
                              $newPrice = roundEx($arItem["PRICE"] - $discountPlus / $arItem["QUANTITY"], 1);
                              $newDiscount = roundEx($arItem["DISCOUNT_PRICE"] + $discountPlus / $arItem["QUANTITY"], 1);
                              $arResult['BONUS']['BONUS_DATA'][$arItem['ID']] = [
                                  0 => roundEx(100 - (($newPrice * 100) / $arItem["BASE_PRICE"]), 1),
                                  1 => $discountPlus,
                              ];
                           } else {
                              if ($arResult['BonusPayResult'][$productID]) {
                                 $discountPlus = $arResult['BonusPayResult'][$productID];
                              } else {
                                 $percent = ($pf * 100) / $order_cart_sum;
                                 $discountPlus = roundEx((($arItem["PRICE"] * $arItem["QUANTITY"] * $percent) / 100), 1);
                              }

                              $newPrice = roundEx($arItem["PRICE"] - $discountPlus / $arItem["QUANTITY"], 1);
                              $newDiscount = roundEx($arItem["DISCOUNT_PRICE"] + $discountPlus / $arItem["QUANTITY"], 1);
                              $arResult['BONUS']['BONUS_DATA'][$arItem['ID']] = [
                                  0 => roundEx(100 - (($newPrice * 100) / $arItem["BASE_PRICE"]), 1),
                                  1 => $discountPlus,
                              ];
                              $arResult['BONUS_DATA'][$arItem['ID']] = [
                                  0 => roundEx(100 - (($newPrice * 100) / $arItem["BASE_PRICE"]), 1),
                                  1 => $discountPlus,
                              ];
                           }
                           $OldValuesOffers[$arItem['ID']] = [
                               'PRICE' => $arItem['PRICE'],
                               'BASE_PRICE' => $arItem['BASE_PRICE'],
                               'DISCOUNT_PRICE' => $arItem['DISCOUNT_PRICE']
                           ];
                           $basketItem->setField('CUSTOM_PRICE', 'Y');
                           $basketItem->setField('PRICE', $newPrice);
                           $basketItem->setField('BASE_PRICE', $arItem["BASE_PRICE"]);
                           $basketItem->setField('DISCOUNT_PRICE', $newDiscount);
                           $newDiscountAll += $newDiscount;
                           $arFields['BASKET'][$arItem['PRODUCT_ID']]['BONUSPAY'] = $discountPlus;

                           $propertyCollection = $basketItem->getPropertyCollection();
                           $propertyItem = $propertyCollection->createItem();
                           $result = $propertyItem->setFields(array('NAME' => $bonuspay_show_title, 'CODE' => 'BONUSPAY', 'VALUE' => $this->ReturnCurrency($discountPlus), 'SORT' => 100));
                           $sum += $newPrice * $arItem["QUANTITY"];
                        } else {
                           $sum += $arItem["PRICE"] * $arItem["QUANTITY"];
                        }
                        unset($propertyCollection, $result, $propertyItem);
                     }
                  }
               }
               $sum += $shipPrice;
               $payment = $order->getPaymentCollection();
               foreach ($payment as $Pay) {
                  if (!$Pay->isInner()) {
                     $Pay->setField("SUM", $sum);
                     if ($sum <= 0) {
                        $Pay->setField("PAID", "Y");
                     }
                  }
               }
            }
         }
      }
      static::$OLDOFFERS = $OldValuesOffers;
      $discount_order_list = $order->getDiscount()->getApplyResult();
      $arFields['DISCOUNT_ORDER_LIST'] = $discount_order_list['FULL_DISCOUNT_LIST'];
      if (!$arFields['DISCOUNT_ORDER_LIST'] && $discount_order_list['DISCOUNT_LIST']) {
         $arFields['DISCOUNT_ORDER_LIST'] = $discount_order_list['DISCOUNT_LIST'];
      }
      $arResult['BONUS']['BONUSORDER'] = $this->GetCartOrderBonus("ORDER", $arFields);
      foreach (GetModuleEvents($this->module_id, "OnBonusEpilogOrderCreated", true) as $arEvent)
         ExecuteModuleEventEx($arEvent, array($order, &$arUserResult, $request, &$arParams, &$arResult, $this));
   }

   public function OnSaleComponentOrderOneStepComplete($ID, $arOrder, $arParams) {
      foreach (GetModuleEvents($this->module_id, "OnBonusPrologOrderComplete", true) as $arEvent)
         ExecuteModuleEventEx($arEvent, array($ID, $arOrder, $arParams, $this));

      if ($ID != 0 && $ID != '' && $arOrder['ID'] != 0 && $arOrder['ID'] != '') {
         global $USER;
         $request = Context::getCurrent()->getRequest();
         $this->SITE_ID = $arOrder['LID'];
         $bonuspay = $request->get('PAY_BONUS_ACCOUNT');
         $userpay = $request->get('PAY_CURRENT_ACCOUNT');
         $bonustopay = $request->get('PAY_BONUSORDERPAY');
         $currentBudget = 0;
         $bon1 = 0;
         $bon2 = 0;
         $arFields = $this->GetArrayForProfile(0, array(), 1, false, $ID);
         $pf = 0;
         if ($this->CheckSiteOn()) {
            $PAYPROF = $this->GetBonusPayProfile($arFields);
            $propm = array();
            if ($this->CheckArray($PAYPROF)) {
               $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
               foreach ($PAYPROF as $prof) {
                  $lp = array();
                  $settings = $this->CheckSerialize($prof['SETTINGS']);
                  $bonusConfig = $this->CheckSerialize($prof['BONUSCONFIG']);
                  $ac = $bonusConfig['BONUSINNERIN']['BONUSINNER'] ? $bonusConfig['BONUSINNERIN']['BONUSINNER'] : $bonusConfig['BONUSINNEROUT']['BONUSINNER'];
                  if ($ac == 0 || $ac == '') {
                     $ac = \ITRound\Vbchbbonus\CvbchBonusPayment::GetBonusPayment();
                     $f = Vbchbbonus\CVbchBonusaccountsTable::getList(array(
                                 'filter' => array('PAYSYSTEMID' => $ac),
                                 'select' => array('ID'),
                             ))->fetch();
                     $ac = $f['ID'];
                  }
                  $MAXPAY = intval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
                  if ($settings['SALEORDERAJAX'] == 'SYSTEMPAY' && $userpay == 'Y') {
                     $bon2 = $request->get("ACCOUNT_CNT");

                     $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $arFields['USERID'], 'CURRENCY' => $arOrder['CURRENCY']));
                     if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $CB1 = round($arUserAccount['CURRENT_BUDGET'], 2);
                        //if ($CB1 < $MAXPAY) $MAXPAY = $CB1;
                        //if ($bon2 >= $MAXPAY) $bon2 = $MAXPAY;
                        //if ($CB1 > 0 && $bon2 > $CB1) $bon2 = 0;
                     }
                  } else {
                     $bon2 = $arOrder['SUM_PAID'];
                  }
                  $bon2_s = $this->Bonusis($bonusConfig, $bon2, "off");
                  $currentBudget += $bon2;
                  if ($bon2 > 0) {
                     if ($bonustopay == 'Y' && $settings['BONUSORDERPAY'] == 'Y') {
                        $arFields['BONUSPAY'] = $currentBudget;
                        $orderBonus = $this->GetCartOrderBonus('ORDER', $arFields);
                        $bonus = $this->BonusParams($orderBonus, $this->CheckSerialize($prof['BONUSCONFIG']));
                        $this->AddBonus($bonus, array('SITE_ID' => $arFields['LID'], 'ORDER_ID' => $ID, 'USERID' => $arFields['USERID'], 'IDUNITS' => 'PAY_PART_' . $ID), $prof, false, false);
                     }
                     $pf = $pf + floatval($bon2_s);
                     if ($settings['BONUSFORDISCOUNT'] != 'Y') {
                        Vbchbbonus\CvbchBonusPayment::InnerPay($arOrder['ID'], $bon2_s, $pf);
                     }

                     $this->AddBonus(array('bonus' => -$bon2, 'ACTIVE' => 'Y', 'ACTIVE_FROM' => '', 'ACTIVE_TO' => '', 'CURRENCY' => ''), array('DESCRIPTION' => 'PAY PART ORDER#' . $ID, 'ORDER_ID' => $ID, 'SITE_ID' => $arFields['LID'], 'USER_ID' => $arFields['USERID'], 'IDUNITS' => 'PAY_PART_' . $ID, 'MAIL_TEMP' => 'PAY'), $prof, false, false);
                     $trans = Vbchbbonus\TmpTable::getList(
                                     array(
                                         'order' => array('SORT' => 'ASC', 'ACTIVE_TO' => 'DESC'),
                                         'filter' => array('ACTIVE' => "Y", ">BONUS" => 0, "USERID" => $arFields['USERID']),
                                         'select' => array('*'),
                                     )
                             )->fetchAll();
                     $this->Raspred($bon2, $trans);
                     foreach ($trans as $tr) {
                        $l = $tr;
                        // $l['ACTIVE_FROM'] = $l['ACTIVE_TO'] = '';
                        unset($l['ID'], $l['UPDATE_DATE']);
                        Vbchbbonus\TmpTable::update($tr['ID'], $l);
                     }
                     $arParams['BON2'] = $bon2;
                  }
                  if ($settings['SALEORDERAJAX'] == 'BONUSPAY' && $bonuspay == 'Y') {
                     $bon1 = intval($request->get("BONUS_CNT"));
                     if ($bon1 > 0 && $bon1 <= $arFields['ORDER_PRICE']) {
                        $dbUserAccount = Vbchbbonus\AccountTable::getList(array(
                                    'filter' => array('USER_ID' => $arFields['USERID'], 'BONUSACCOUNTSID' => $ac),
                                ))->fetch();
                        $CB = round($dbUserAccount["CURRENT_BUDGET"], 2);
                        $CB = floatval($CB);
                        //if ($CB < $MAXPAY) $MAXPAY = $CB;
                        //if ($bon1 >= $MAXPAY) $bon1 = $MAXPAY;
                        //if ($CB > 0 && $bon1 > $CB) $bon1 = 0;
                        $currentBudget += $bon1;
                     }
                     if ($settings['WITHOUTPAYBONUS'] == 'Y') {
                        $arFields['BONUSPAY'] = $currentBudget;
                     }
                     if ($bonustopay == 'Y' && $settings['BONUSORDERPAY'] == 'Y') {
                        $arFields['BONUSPAY'] = $currentBudget;
                        $orderBonus = $this->GetCartOrderBonus('ORDER', $arFields);
                        $this->AddBonus(array('bonus' => $orderBonus, 'ACTIVE' => 'Y', 'ACTIVE_FROM' => '', 'ACTIVE_TO' => '', 'CURRENCY' => ''), array('ORDER_ID' => $ID, 'SITE_ID' => $arFields['LID'], 'USER_ID' => $arFields['USERID'], 'IDUNITS' => 'Add_PAY_PART_' . $ID), $prof, false, false);
                     }
                     $bon1_s = $this->Bonusis($bonusConfig, $bon1, "off");
                     $pf = $pf + floatval($bon1_s);
                     if ($settings['BONUSFORDISCOUNT'] != 'Y') {
                        Vbchbbonus\CvbchBonusPayment::InnerBonusPay($arOrder['ID'], $bon1_s, $pf);
                     }
                     $order = \Bitrix\Sale\Order::load(intval($ID));
                     $paymentCollection = $order->getPaymentCollection();
                     $innerPay = $paymentCollection->getInnerPayment();
                     if ($innerPay && $innerPay->getField("SUM") > 0) {
                        foreach ($paymentCollection as $paysystem) {
                           if ($paysystem->getPaymentSystemId() != \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId()) {
                              $currentPay = $paysystem;
                              break;
                           }
                        }
                        $sum = $currentPay->getField("SUM");
                        $sum = $sum - $innerPay->getField("SUM");
                        $currentPay->setField("SUM", $sum);
                        $order->save();
                        unset($order);
                     }
                     $this->AddBonus(array('bonus' => -$bon1, 'ACTIVE' => 'Y', 'ACTIVE_FROM' => '', 'ACTIVE_TO' => '', 'CURRENCY' => ''), array('ORDER_ID' => $ID, 'DESCRIPTION' => 'PAY PART ORDER#' . $ID, 'SITE_ID' => $arFields['LID'], 'USER_ID' => $arFields['USERID'], 'IDUNITS' => 'PAY_PART_' . $ID, 'MAIL_TEMP' => 'PAY'), $prof, false, false);
                     $trans = Vbchbbonus\BonusTable::getList(
                                     array(
                                         'order' => array('SORT' => 'ASC', 'ACTIVE_TO' => 'DESC'),
                                         'filter' => array('ACTIVE' => "Y", ">BONUS" => 0, "USERID" => $arFields['USERID'], 'BONUSACCOUNTSID' => $ac),
                                         'select' => array('*'),
                                     )
                             )->fetchAll();
                     $this->Raspred($bon1, $trans);
                     $arParams['BON1'] = $bon1;
                     foreach ($trans as $tr) {
                        Application::getConnection()->startTransaction();
                        $l = $tr;
                        //$l['ACTIVE_FROM'] = $l['ACTIVE_TO'] = '';
                        unset($l['ID'], $l['UPDATE_DATE']);
                        $res = Vbchbbonus\BonusTable::update($tr['ID'], $l);
                        if ($res->isSuccess()) {
                           Application::getConnection()->commitTransaction();
                        } else {
                           Application::getConnection()->rollbackTransaction();
                        }
                     }
                  }
                  if ($settings['ORDERPROPBONUSPAY']['ACTIVE'] == 'Y') {
                     $propm = array_merge($propm, $settings['ORDERPROPBONUSPAY']['ID']);
                  }
               }
               $propID = $this->ReturnOrderProp($arOrder['PERSON_TYPE_ID'], $propm);
               if ($propID && $currentBudget > 0) {
                  $this->AddOrderProperty($propID, $currentBudget, $arOrder['ID']);
               }


               if ($settings['BONUSFORDISCOUNT'] != 'Y') {
                  if ($arOrder['ID'] > 0 && $currentBudget > 0) {
                     if ($this->isD7()) {
                        \CSaleOrder::Update($arOrder['ID'], array('SUM_PAID' => $currentBudget, 'USER_ID' => $arFields['USERID']));
                        if ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $currentBudget == $arOrder['PRICE']) {
                           \CSaleOrder::PayOrder($arOrder["ORDER_ID"], "Y", false, false);
                        }
                     } else {
                        \CSaleOrder::Update($arOrder['ID'], array('SUM_PAID' => $currentBudget, 'USER_ID' => $arFields['USERID']));
                        if ($arParams['ONLY_FULL_PAY_FROM_ACCOUNT'] == 'Y' && $currentBudget == $arOrder['PRICE']) {
                           \CSaleOrder::PayOrder($arOrder["ORDER_ID"], "Y", false, false);
                        }
                     }
                  }
               }
            }
            $bonusorderinprop = $this->GetOptions($this->SITE_ID, 'STARTBONUINORDERPROP');
            $bonusorderinprop = $bonusorderinprop['OPTION'] == 'Y';
            if ($bonusorderinprop) {
               $this->GetBonusFromOrderID($arOrder['ID']);
            }
         }
      }
      foreach (GetModuleEvents($this->module_id, "OnBonusEpilogOrderComplete", true) as $arEvent)
         ExecuteModuleEventEx($arEvent, array($ID, $arOrder, $arParams, $this));
   }

   public function AddOrderProperty($prop_id, $value, $order) {
      if (!strlen($prop_id)) {
         return false;
      }

      if ($arOrderProps = \CSaleOrderProps::GetByID($prop_id)) {
         $db_vals = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $order, 'ORDER_PROPS_ID' => $arOrderProps['ID']));
         if ($arVals = $db_vals->Fetch()) {
            return \CSaleOrderPropsValue::Update($arVals['ID'], array(
                        'NAME' => $arVals['NAME'],
                        'CODE' => $arVals['CODE'],
                        'ORDER_PROPS_ID' => $arVals['ORDER_PROPS_ID'],
                        'ORDER_ID' => $arVals['ORDER_ID'],
                        'VALUE' => $value,
            ));
         } else {
            return \CSaleOrderPropsValue::Add(array(
                        'NAME' => $arOrderProps['NAME'],
                        'CODE' => $arOrderProps['CODE'],
                        'ORDER_PROPS_ID' => $arOrderProps['ID'],
                        'ORDER_ID' => $order,
                        'VALUE' => $value,
            ));
         }
      }
   }

   public function ReturnOrderProp($PERSON_TYPE, $prop = array()) {
      $db_props = \CSaleOrderProps::GetList(
                      array("SORT" => "ASC"),
                      array('PERSON_TYPE_ID' => $PERSON_TYPE, 'ID' => $prop),
                      false,
                      false,
                      array('ID')
              )->Fetch();
      return $db_props['ID'];
   }

   public function GetBonusPaySystem(&$arResult) {
      $data = \Bitrix\Sale\Internals\PaySystemActionTable::getRow(
                      array(
                          'select' => array('*'),
                          'filter' => array('ACTION_FILE' => 'innerbonus')
                      )
      );
      $data['LOGOTIP'] = \CFile::GetFileArray($data['LOGOTIP']);
      if (!is_array($arResult['BONUSPAY']))
         $arResult['BONUSPAY'] = array();
      $arResult['BONUSPAY'] = array_merge($arResult['BONUSPAY'], $data);

      $arResult['BONUSPAY']['LOGOTIP_SRC'] = $arResult['BONUSPAY']['LOGOTIP']['SRC'];
   }

   public function GetIDChecked($mas = array()) {
      if ($this->CheckArray($mas)) {
         foreach ($mas as $m) {
            if (array_key_exists("CHECKED", $m) && $m['CHECKED'] == 'Y')
               return $m['ID'];
         }
      }
   }

   public function Bonusis($config, $value, $type) {
      $BONUSIS = floatval(($config['BONUSIS'] != "") ? $config['BONUSIS'] : 1);
      if ($type == "on")
         $value = floatval($value * $BONUSIS);
      elseif ($type == "off")
         $value = floatval($value / $BONUSIS);
      if ($config['ROUND'] == 'PHP_ROUND_HALF_UP')
         $l = PHP_ROUND_HALF_UP;
      if ($config['ROUND'] == 'PHP_ROUND_HALF_DOWN')
         $l = PHP_ROUND_HALF_DOWN;
      if ($config['ROUND'] == 'PHP_ROUND_HALF_EVEN')
         $l = PHP_ROUND_HALF_EVEN;
      if ($config['ROUND'] == 'PHP_ROUND_HALF_ODD')
         $l = PHP_ROUND_HALF_ODD;
      $value = round($value, $config['PRECISION'] ? $config['PRECISION'] : 0, $l);
      if ($config['ROUNDONE'] == 'Y') {
         if ($value < 1 && $value > 0)
            $value = 1;
      }
      return $value;
   }

   public function OnSaleComponentOrderOneStepProcess(\Bitrix\Sale\Order $order, &$arResult, $arUserResult, &$arParams, $request) {
      $BBCORE = $this;
      foreach (GetModuleEvents($this->module_id, "OnBonusPrologOrderProcess", true) as $arEvent)
         ExecuteModuleEventEx($arEvent, array($order, &$arResult, $arUserResult, &$arParams, $request, $BBCORE));

      $k = array();
      $idp = false;
      foreach ($arResult['PAY_SYSTEM'] as $psid => $ps) {
         if ($ps['CHECKED'] == 'Y') {
            $idp = $ps['ID'];
         }
         if ($ps['PSA_ACTION_FILE'] != 'innerbonus') {
            $k[] = $ps;
         }
      }
      if ($idp) {
         foreach ($k as $r => $pp) {
            if ($pp['ID'] == $idp)
               $k[$r]['CHECKED'] = 'Y';
            else {
               $k[$r]['CHECKED'] = 'N';
            }
         }
      } else {
         $k[0]['CHECKED'] = 'Y';
      }

      $arResult['PAY_SYSTEM'] = $k;
      $arResult['JS_DATA']['PAY_SYSTEM'] = $k;
      unset($k);
      $basket = $order->getBasket();
      foreach ($basket as $basketItem) {
         $item = $basketItem->getFields();
         $arItems[$basketItem->getId()] = $item->getValues();
      }
      $tmp = $this->CreateArFields($arItems);
      $allOFFER = $this->GetPropAndDiscount($tmp['ONE'], $tmp['TWO']);
      global $USER;
      $this->SITE_ID = SITE_ID;
      $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
      $arFields = $this->GetArrayForProfile(0, array(), 1, false, 0);
      $arFields['BASKET'] = $allOFFER;
      $arFields['DELIVERY_PRICE'] = $arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE'];
      $arFields['ORDER_PRICE'] = floatval($arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE']) - floatval($arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE']);
      $arFields['DELIVERY_ID'] = $this->GetIDChecked($arResult['JS_DATA']['DELIVERY']);
      $arFields['PAY_SYSTEM_ID'] = $this->GetIDChecked($arResult['JS_DATA']['PAY_SYSTEM']);
      $arFields['PERSON_TYPE_ID'] = $this->GetIDChecked($arResult['JS_DATA']['PERSON_TYPE']);
      $arFields['RUN_ACTION'] = false;
      //$arResult['BONUS']['BONUSORDER'] = $this->GetCartOrderBonus("ORDER", $arFields); //TIKET 2800

      if ($arParams['SHOW_BONUS_SYSTEM'] == 'Y') {
         $discount_order_list = $order->getDiscount()->getApplyResult();
         $arFields['DISCOUNT_ORDER_LIST'] = $discount_order_list['FULL_DISCOUNT_LIST'];
         if (!$arFields['DISCOUNT_ORDER_LIST'] && $discount_order_list['DISCOUNT_LIST']) {
            $arFields['DISCOUNT_ORDER_LIST'] = $discount_order_list['DISCOUNT_LIST'];
         }
         $l = array();
         $l['BONUSPAY'] = array();

         $this->GetBonusPaySystem($l);
         $arResult['JS_DATA']['INNER_BONUS'] = $l;
         $arResult['JS_DATA']['PAY_BONUSORDERPAY'] = ($request->get('PAY_BONUSORDERPAY') == 'Y');
         //$arResult['JS_DATA']['PAY_CURRENT_ACCOUNT'] = ($request->get('PAY_CURRENT_ACCOUNT') == 'Y');
         $arResult['JS_DATA']['PAY_BONUS_ACCOUNT'] = ($request->get('PAY_BONUS_ACCOUNT') == 'Y');
         $bonuspaysum = false;
         if ($this->CheckSiteOn()) {
            $PAYPROF = $this->GetBonusPayProfile($arFields);
            $userInput = $inputUserB = 0;
            $allSumm = $arFields['ORDER_PRICE'] + $arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE'];
            if ($this->CheckArray($PAYPROF)) {
               $maxpay = 0;
               foreach ($PAYPROF as $prof) {
                  $maxpay += floatval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
               }
               foreach ($PAYPROF as $prof) {
                  $lp = array();
                  $settings = array();
                  $settings = $this->CheckSerialize($prof['SETTINGS']);

                  $bonusconfig = $this->CheckSerialize($prof['BONUSCONFIG']);
                  $orderTotalSum = ($settings['WITHOUTDELIVERYPRICE'] == 'Y' ? $arResult['ORDER_PRICE'] : $arResult['ORDER_PRICE'] + $arResult['DELIVERY_PRICE']);
                  $arResult['TYPEPAY'][] = $settings['SALEORDERAJAX'];
                  $lp['MAXPAY'] = $maxpay; //intval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
                  if ($arResult['BONUS']['PAYBONUS'])
                     $lp['MAXPAY'] = $arResult['BONUS']['PAYBONUS'];
                  $lp['ORDER_PAY_PERCENT'] = $prof['BONUS'];
                  $lp['USER_INPUT'] = ($settings['INPUTUSER'] == 'Y');
                  $lp['DISPLAY'] = true;
                  $lp['BONUSORDERPAY'] = ($settings['BONUSORDERPAY'] == 'Y');
                  if ($prof['SCOREIN'] == 'Y' && $settings['SALEORDERAJAX'] == 'SYSTEMPAY') { //inner user accounts
                     $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $arFields['USERID'], 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
                     $userInput = floatval($request->get('ACCOUNT_CNT'));
                     if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $currentBudget = round($arUserAccount['CURRENT_BUDGET'], 2);
                        if ($currentBudget <= 0)
                           unset($arResult['TYPEPAY'][array_search('SYSTEMPAY', $arResult['TYPEPAY'])]);
                        if ($lp['MAXPAY'] > $currentBudget)
                           $lp['MAXPAY'] = $currentBudget;
                        $lp["CURRENT_BUDGET_FORMATED"] = $this->ReturnCurrency($currentBudget);
                        $lp["USER_ACCOUNT"] = $arUserAccount;

                        if ($arResult['JS_DATA']['PAY_BONUSORDERPAY'] && $settings['BONUSORDERPAY'] == 'Y') {
                           $currentBudget += $this->GetCartOrderBonus('ORDER');
                           $lp['MAXPAY'] = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields));
                           if ($lp['MAXPAY'] > $currentBudget)
                              $lp['MAXPAY'] = $currentBudget;
                           $lp["CURRENT_BUDGET_FORMATED"] = $this->ReturnCurrency($currentBudget);
                           $lp["USER_ACCOUNT"] = $arUserAccount;
                           $lp["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($currentBudget >= $orderTotalSum) ? $orderTotalSum : $currentBudget), $arResult["BASE_LANG_CURRENCY"]);
                           $lp["MAXPAY_FORMATTED"] = $this->ReturnCurrency($lp['MAXPAY']);
                           $arResult['JS_DATA'][$settings['SALEORDERAJAX']] = $lp;
                        }
                        if ($arResult['JS_DATA']['PAY_CURRENT_ACCOUNT']) {
                           $lp["PAYED_FROM_ACCOUNT_FORMATED"] = $arResult['JS_DATA']['TOTAL']['ACCOUNT_PAY'] = SaleFormatCurrency((($currentBudget >= $orderTotalSum) ? $orderTotalSum : $currentBudget), $arResult["BASE_LANG_CURRENCY"]);
                           if ($settings['INPUTUSER'] == 'Y' && $currentBudget > 0) {
                              if (!empty($userInput) && $userInput != '' && $userInput != 0 && $userInput > 0) {
                                 if ($userInput > $currentBudget)
                                    $userInput = floatval($currentBudget);
                                 if ($userInput > $orderTotalSum)
                                    $userInput = $orderTotalSum;
                                 if ($userInput > $lp['MAXPAY'])
                                    $userInput = floatval($lp['MAXPAY']);
                              } else {
                                 $userInput = ($lp['MAXPAY'] > $currentBudget) ? $currentBudget : floatval($lp['MAXPAY']);
                              }
                              if ($userInput < $lp['MAXPAY'])
                                 $lp['MAXPAY'] = $userInput;
                           } else {
                              $userInput = ($lp['MAXPAY'] > $currentBudget) ? $currentBudget : floatval($lp['MAXPAY']);
                           }
                           if ($arResult['BONUS']['PAYBONUS'])
                              $userInput = $arResult['BONUS']['PAYBONUS'];
                           $userInput = $this->Bonusis($bonusconfig, $userInput, "off");
                           $allSumm = $allSumm - $userInput;
                           $lp["PAYED_FROM_ACCOUNT_FORMATED"] = $arResult['JS_DATA']['TOTAL']['ACCOUNT_PAY'] = SaleFormatCurrency($userInput, $arResult["BASE_LANG_CURRENCY"]);
                        } else
                           $userInput = 0;
                        $lp["MAXPAY_FORMATTED"] = $this->ReturnCurrency($lp['MAXPAY']);
                        $arResult['JS_DATA'][$settings['SALEORDERAJAX']] = $lp;
                     } else {
                        $lp['MAXPAY'] = 0;
                        $lp['DISPLAY'] = false;
                        $lp["CURRENT_BUDGET_FORMATED"] = 0;
                        $lp["PAYED_FROM_ACCOUNT_FORMATED"] = $arResult['JS_DATA']['TOTAL']['ACCOUNT_PAY'] = 0;
                        $lp["MAXPAY_FORMATTED"] = $this->ReturnCurrency($lp['MAXPAY']);
                        $arResult['JS_DATA'][$settings['SALEORDERAJAX']] = $lp;
                     }
                  }
                  if ($prof['SCOREIN'] != 'Y' && $settings['SALEORDERAJAX'] == 'BONUSPAY') { //module bonus accounts
                     $inputUserB = floatval($request->get('BONUS_CNT'));
                     $dbUserAccount = AccountTable::getList(array(
                                 'filter' => array('USER_ID' => $USER->GetID(), 'BONUSACCOUNTSID' => $bonusconfig['BONUSINNEROUT']['BONUSINNER']),
                             ))->fetch();
                     if ($this->CheckArray($dbUserAccount)) {
                        $currentBonusBudget = round($dbUserAccount["CURRENT_BUDGET"], 2);
                        if ($currentBonusBudget <= 0) {
                           unset($arResult['TYPEPAY'][array_search('BONUSPAY', $arResult['TYPEPAY'])]);
                        }
                        $currentBonusBudget = floatval($currentBonusBudget);
                        if ($lp['MAXPAY'] > $currentBonusBudget)
                           $lp['MAXPAY'] = $currentBonusBudget;
                        $lp["CURRENT_BONUS_BUDGET_FORMATED"] = $this->ReturnCurrency($currentBonusBudget);
                        $lp["USER_ACCOUNT"] = array('USER_ID' => $USER->GetID());
                        if ($arResult['JS_DATA']['PAY_BONUSORDERPAY'] && $settings['BONUSORDERPAY'] == 'Y') {
                           $currentBonusBudget += $this->GetCartOrderBonus('ORDER');
                           $lp['MAXPAY'] = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields));
                           if ($lp['MAXPAY'] > $currentBonusBudget)
                              $lp['MAXPAY'] = $currentBonusBudget;

                           $lp["CURRENT_BONUS_BUDGET_FORMATED"] = $this->ReturnCurrency($currentBonusBudget);
                           $lp["USER_ACCOUNT"] = array('USER_ID' => $USER->GetID());
                           $lp["PAYED_FROM_BONUS_FORMATED"] = $this->ReturnCurrency($currentBonusBudget >= $orderTotalSum ? $orderTotalSum : $currentBonusBudget);
                           if ($inputUserB < $lp['MAXPAY'])
                              $lp['MAXPAY'] = $inputUserB;
                           $lp["MAXPAY_FORMATTED"] = $this->ReturnCurrency($lp['MAXPAY']);
                           $arResult['JS_DATA'][$settings['SALEORDERAJAX']] = $lp;
                        }
                     } else {
                        $lp['MAXPAY'] = 0;
                        $lp['DISPLAY'] = false;
                        $lp["CURRENT_BONUS_BUDGET_FORMATED"] = 0;
                        $lp["PAYED_FROM_BONUS_FORMATED"] = $arResult['JS_DATA']['TOTAL']['ACCOUNT_PAY'] = 0;
                        $lp["MAXPAY_FORMATTED"] = $this->ReturnCurrency($lp['MAXPAY']);
                        $arResult['JS_DATA'][$settings['SALEORDERAJAX']] = $lp;
                     }
                     if ($arResult['JS_DATA']['PAY_BONUS_ACCOUNT']) {
                        $lp["PAYED_FROM_BONUS_FORMATED"] = $arResult['JS_DATA']['TOTAL']['BONUS_PAY'] = $this->ReturnCurrency($currentBonusBudget >= $orderTotalSum ? $orderTotalSum : $currentBonusBudget);
                        if ($settings['INPUTUSER'] == 'Y' && $currentBonusBudget > 0) {
                           if (!empty($inputUserB) && $inputUserB != '' && $inputUserB != 0 && $inputUserB > 0) {
                              if ($inputUserB > $currentBonusBudget)
                                 $inputUserB = intval($currentBonusBudget);
                              if ($inputUserB > $orderTotalSum)
                                 $inputUserB = $orderTotalSum;
                              if ($inputUserB > $lp['MAXPAY'])
                                 $inputUserB = floatval($lp['MAXPAY']);
                           } else {
                              $inputUserB = ($lp['MAXPAY'] > $currentBonusBudget) ? $currentBonusBudget : floatval($lp['MAXPAY']);
                           }
                        } else {
                           $inputUserB = ($lp['MAXPAY'] > $currentBonusBudget) ? $currentBonusBudget : floatval($lp['MAXPAY']);
                        }

                        $BONUSIS = floatval(($bonusconfig['BONUSIS'] != "") ? $bonusconfig['BONUSIS'] : 1);
                        $inputUserB1 = floatval($inputUserB / $BONUSIS);
                        $roundMode = '';
                        if ($bonusconfig['ROUND'] == 'PHP_ROUND_HALF_UP')
                           $roundMode = PHP_ROUND_HALF_UP;
                        if ($bonusconfig['ROUND'] == 'PHP_ROUND_HALF_DOWN')
                           $roundMode = PHP_ROUND_HALF_DOWN;
                        if ($bonusconfig['ROUND'] == 'PHP_ROUND_HALF_EVEN')
                           $roundMode = PHP_ROUND_HALF_EVEN;
                        if ($bonusconfig['ROUND'] == 'PHP_ROUND_HALF_ODD')
                           $roundMode = PHP_ROUND_HALF_ODD;


                        $inputUserB1 = round($inputUserB1, $bonusconfig['PRECISION'] ? $bonusconfig['PRECISION'] : 0, $roundMode);
                        $inputUserB = round($inputUserB, $bonusconfig['PRECISION'] ? $bonusconfig['PRECISION'] : 0, $roundMode);

                        if ($bonusconfig['ROUNDONE'] == 'Y') {
                           if ($inputUserB1 < 1 && $inputUserB1 > 0)
                              $inputUserB1 = 1;
                        }
                        if ($arResult['BONUS']['PAYBONUS'])
                           $inputUserB1 = $arResult['BONUS']['PAYBONUS'];
                        if ($settings['BONUSFORDISCOUNT'] !== 'Y')
                           $allSumm = $allSumm - $inputUserB1;

                        if ($inputUserB1 < $lp['MAXPAY'])
                           $lp['MAXPAY'] = $inputUserB1;
                        if ($BONUSIS != 0 || $BONUSIS != '') {
                           $lp['MAXPAY'] = floatval($lp['MAXPAY'] * $BONUSIS);
                        }
                        $lp["PAYED_FROM_BONUS_FORMATED"] = $arResult['JS_DATA']['TOTAL']['BONUS_PAY'] = SaleFormatCurrency($inputUserB1, $arResult["BASE_LANG_CURRENCY"]); //$this->ReturnCurrency($inputUserB1);
                     } else
                        $inputUserB1 = 0;
                  }
                  $lp["MAXPAY_FORMATTED"] = $this->ReturnCurrency($lp['MAXPAY']);
                  $arResult['JS_DATA'][$settings['SALEORDERAJAX']] = $arResult[$settings['SALEORDERAJAX']] = $lp;
                  $this->GetBonusPaySystem($arResult['JS_DATA']);
               }
               if ($settings['BONUSFORDISCOUNT'] !== 'Y')
                  $arFields['BONUSPAY'] = $inputUserB1 + $userInput;
               if ($arResult['JS_DATA']['PAY_BONUSORDERPAY'])
                  $arResult['JS_DATA']['TOTAL']['BONUS_ORDER'] = $this->ReturnCurrency(0);
               else {
                  $bns = $arResult['BONUS']['BONUSORDER'];
                  $bns = $bns < 0 ? 0 : $bns;
                  $arResult['JS_DATA']['TOTAL']['BONUS_ORDER'] = $arResult['BONUSORDERPAY']['BONUSORDERPAY_FORMATED'] = $this->ReturnCurrency($bns);
               }
               $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE'] = $allSumm;
               $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE_FORMATED'] = SaleFormatCurrency($allSumm, $arResult["BASE_LANG_CURRENCY"]);
            }
            $bns = $arResult['BONUS']['BONUSORDER'];
            $bns = $bns < 0 ? 0 : $bns;
            $arResult['JS_DATA']['TOTAL']['BONUS_ORDER'] = $arResult['BONUSORDERPAY']['BONUSORDERPAY_FORMATED'] = $this->ReturnCurrency($bns);
         }
      } else {
         $arResult['JS_DATA']['TOTAL']['BONUS_ORDER'] = $arResult['BONUSORDERPAY']['BONUSORDERPAY_FORMATED'] = $this->ReturnCurrency($arResult['BONUS']['BONUSORDER']);
      }
      $bonus_show = (\Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERBONUSSHOW', "", $this->SITE_ID) == 'Y');
      $bonuspay_show = (\Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERBONUSPAYSHOW', "", $this->SITE_ID) == 'Y');
      $bonus_show_title = (\Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERBONUSTITLE', "", $this->SITE_ID));
      $bonuspay_show_title = (\Bitrix\Main\Config\Option::get('vbcherepanov.bonus', 'INTEGRATIONORDERBONUSPAYTITLE', "", $this->SITE_ID));
      if ($bonus_show) {
         $arResult['JS_DATA']['GRID']['HEADERS'][] = ['id' => 'BONUS', 'name' => $bonus_show_title];
      }
      if (is_array($arResult['BONUS_DATA']) && !empty($arResult['BONUS_DATA'])) {
         if ($bonuspay_show)
            $arResult['JS_DATA']['GRID']['HEADERS'][] = ['id' => 'BONUSPAY', 'name' => $bonuspay_show_title];
         foreach ($arResult['JS_DATA']['GRID']['ROWS'] as $key => &$js_item) {
            if ($bonuspay_show)
               $js_item['columns']['BONUSPAY'] = $this->ReturnCurrency($arResult['BONUS_DATA'][$key][1]);
            $js_item['data']['DISCOUNT_PRICE_PERCENT_FORMATED'] = $arResult['BONUS_DATA'][$key][0] . '%';
            unset($js_item);
         }
      }
      if ($bonus_show)
         foreach ($arResult['JS_DATA']['GRID']['ROWS'] as $key => &$js_item) {
            $js_item['columns']['BONUS'] = $arResult['ELEMENT_BONUS_DATA'][$key][2];
            unset($js_item);
         }
      foreach (GetModuleEvents($this->module_id, "OnBonusEpilogOrderProcess", true) as $arEvent)
         ExecuteModuleEventEx($arEvent, array($order, &$arResult, $arUserResult, &$arParams, $request, $BBCORE));

      unset($BBCORE);
   }

   public function RefreshPayedFromAccount(&$arResult) {
	    $arOptionEnableOldPayFunction = $this->GetOptions($this->SITE_ID, 'ENABLE_OLD_PAY_FUNCTION');
      if ($arOptionEnableOldPayFunction['OPTION'] != 'Y') {
         return false;
      }
		if (function_exists("RefreshPayedFromAccountCustom")) {
         RefreshPayedFromAccountCustom($this, $arResult);
      } else {
         $arResult['PAYED_FROM_ACCOUNT_FORMATED'] = '';
         global $USER;
         $option = $this->GetOptions($this->SITE_ID, 'BONUSNAME');
         $bonusToPay = ($arResult['USER_VALS']['PAY_BONUSORDERPAY'] == 'Y');
         if ($this->CheckSiteOn() && $this->AuthorizedUser()) {
            if ($this->NEW_SALE_ORDER_AJAX) {
               $arFields = array(
                   'USERID' => $arResult['ORDER_DATA']['USER_ID'] ? $arResult['ORDER_DATA']['USER_ID'] : $USER->GetID(),
                   'BASKET' => $arResult['BASKET_ITEMS'],
                   'ORDER_DATA' => array(
                       'ORDER_DATA' => $arResult['ORDER_DATA'],
                       'USER_VALS' => $arResult['USER_VALS'],
                       'WITHOUT_DISCOUNT' => $arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] ? $arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] : ($arResult['ORDER_PRICE'] ? $arResult['ORDER_PRICE'] : $arResult['PRICE']),
                       'DELIVERY_PRICE' => $arResult['DELIVERY_PRICE'],
                       'BASE_LANG_CURRENCY' => $arResult['BASE_LANG_CURRENCY'],
                   ),
               );
            } else {
               $arFields = array(
                   'USERID' => $arResult['ORDER_DATA']['USER_ID'] ? $arResult['ORDER_DATA']['USER_ID'] : $USER->GetID(),
                   'BASKET' => $arResult['BASKET_ITEMS'],
                   'ORDER_DATA' => array(
                       'ORDER_DATA' => $arResult['ORDER_DATA'],
                       'USER_VALS' => $arResult['USER_VALS'],
                       'WITHOUT_DISCOUNT' => $arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] ? $arResult['PRICE_WITHOUT_DISCOUNT_VALUE'] : ($arResult['ORDER_PRICE'] ? $arResult['ORDER_PRICE'] : $arResult['PRICE']),
                       'DELIVERY_PRICE' => $arResult['DELIVERY_PRICE'],
                       'BASE_LANG_CURRENCY' => $arResult['BASE_LANG_CURRENCY'],
                   ),
               );
            }
            $arFields['USERGROUP'] = $this->GetUserGroupByUser($arFields['USERID']);
            $arFields['DELIVERY_PRICE'] = $arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE'];
            $arFields['ORDER_PRICE'] = floatval($arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_PRICE']) - floatval($arResult['JS_DATA']['TOTAL']['DELIVERY_PRICE']);
            $arFields['DELIVERY_ID'] = $this->GetIDChecked($arResult['JS_DATA']['DELIVERY']);
            $arFields['PAY_SYSTEM_ID'] = $this->GetIDChecked($arResult['JS_DATA']['PAY_SYSTEM']);
            $arFields['PERSON_TYPE_ID'] = $this->GetIDChecked($arResult['JS_DATA']['PERSON_TYPE']);
            $PAYPROF = $this->GetBonusPayProfile($arFields);
            if ($this->CheckArray($PAYPROF)) {
               $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
               foreach ($PAYPROF as $prof) {
                  $lp = array();
                  $bonusconfig = $this->CheckSerialize($prof['BONUSCONFIG']);
                  $settings = $this->CheckSerialize($prof['SETTINGS']);
                  $orderTotalSum = ($settings['WITHOUTDELIVERYPRICE'] == 'Y' ? $arResult['ORDER_PRICE'] : $arResult['ORDER_PRICE'] + $arResult['DELIVERY_PRICE']);
                  $MAXpay = intval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
                  $lp['ORDER_PAY_PERCENT'] = $prof['BONUS'];
                  $lp['USER_INPUT'] = ($settings['INPUTUSER'] == 'Y');
                  $lp['BONUSORDERPAY'] = ($settings['BONUSORDERPAY'] == 'Y');
                  if ($prof['SCOREIN'] == 'Y' && $settings['SALEORDERAJAX'] == 'SYSTEMPAY') {
                     $dbUserAccount = \CSaleUserAccount::GetList(array(), array('USER_ID' => $USER->GetID(), 'CURRENCY' => $arResult['BASE_LANG_CURRENCY']));
                     if ($arUserAccount = $dbUserAccount->GetNext()) {
                        $currentBudgetAccount = $arUserAccount['CURRENT_BUDGET'];
                     } else {
                        $currentBudgetAccount = 0;
                     }
                     if ($bonusToPay && $settings['BONUSORDERPAY'] == 'Y') {
                        $currentBudgetAccount += $this->GetCartOrderBonus('ORDER');
                        $lp['MAXPAY'] = intval($MAXpay); //intval(call_user_func_array(array($this->INSTALL_PROFILE['BONUSPAY'], "GetBonus"), array($this->FUNC_GETBONUS['BONUSPAY'], $prof, $arFields)));
                        if ($lp['MAXPAY'] > $currentBudgetAccount)
                           $lp['MAXPAY'] = intval($currentBudgetAccount);
                        $lp["CURRENT_BUDGET_FORMATED"] = $this->declOfNum($currentBudgetAccount, ($option['OPTION']['SUFIX'] == 'NAME' ? $option['OPTION']['NAME'] : array("", "", "")), $this->ModuleCurrency());
                        $lp["USER_ACCOUNT"] = $arUserAccount;
                        $lp["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($currentBudgetAccount >= $orderTotalSum) ? $orderTotalSum : $currentBudgetAccount), $arResult["BASE_LANG_CURRENCY"]);
                        $arResult[$settings['SALEORDERAJAX']] = $lp;
                     }
                     if ($currentBudgetAccount <= 0)
                        $arResult['PAY_FROM_ACCOUNT1'] = 'N';
                     else
                        $arResult['PAY_FROM_ACCOUNT1'] = 'Y';
                     if ($settings['INPUTUSER'] == 'Y' && $currentBudgetAccount > 0) {
                        //$arResult['PAY_FROM_ACCOUNT']='N';
                        $arResult['PAY_FROM_ACCOUNT1'] = 'Y';
                        $inputUser = intval(htmlspecialchars($_POST["ACCOUNT_CNT"]));
                        if (!empty($inputUser) && $inputUser != '' && $inputUser != 0 && $inputUser > 0) {
                           if ($inputUser > $currentBudgetAccount)
                              $inputUser = intval($currentBudgetAccount);
                           if ($inputUser > $orderTotalSum)
                              $inputUser = $orderTotalSum;
                           if ($inputUser > $MAXpay)
                              $inputUser = intval($MAXpay);
                        } else {
                           $inputUser = ($MAXpay > $currentBudgetAccount) ? $currentBudgetAccount : intval($MAXpay);
                        }
                     } else {
                        $inputUser = intval(($MAXpay > $currentBudgetAccount) ? $currentBudgetAccount : intval($MAXpay));
                     }
                  } elseif ($prof['SCOREIN'] !== 'Y' && $settings['SALEORDERAJAX'] == 'BONUSPAY') {
                     $dbUserAccount = Vbchbbonus\AccountTable::getList(array(
                                 'filter' => array('USER_ID' => $USER->GetID(), 'BONUSACCOUNTSID' => $bonusconfig['BONUSINNEROUT']['BONUSINNER']),
                     ));
                     if ($arUserAccount = $dbUserAccount->fetch())
                        $currentBonusBudget = $arUserAccount["CURRENT_BUDGET"];
                     else
                        $currentBonusBudget = 0;
                     if ($bonusToPay && $settings['BONUSORDERPAY'] == 'Y') {
                        $currentBonusBudget += $this->GetCartOrderBonus('ORDER');
                        $lp['MAXPAY'] = intval(call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetBonus"), array($this->FUNC_GETBONUS[$prof['TYPE']], $prof, $arFields)));
                        if ($lp['MAXPAY'] > $currentBonusBudget)
                           $lp['MAXPAY'] = intval($currentBonusBudget);
                        $lp["CURRENT_BONUS_BUDGET_FORMATED"] = $this->declOfNum($currentBonusBudget, ($option['OPTION']['SUFIX'] == 'NAME' ? $option['OPTION']['NAME'] : array("", "", "")), $this->ModuleCurrency());
                        $lp["USER_ACCOUNT"] = array('USER_ID' => $USER->GetID());
                        $lp["PAYED_FROM_BONUS_FORMATED"] = $this->declOfNum($currentBonusBudget >= $orderTotalSum ? $orderTotalSum : $currentBonusBudget, ($option['OPTION']['SUFIX'] == 'NAME' ? $option['OPTION']['NAME'] : array("", "", "")), $this->ModuleCurrency());
                        $arResult[$settings['SALEORDERAJAX']] = $lp;
                     }
                     if ($currentBonusBudget <= 0)
                        $arResult['PAY_FROM_BONUS'] = 'N';
                     else
                        $arResult['PAY_FROM_BONUS'] = 'Y';
                     if ($settings['INPUTUSER'] == 'Y' && $currentBonusBudget > 0) {
                        $arResult['PAY_FROM_BONUS'] = 'Y';
                        $inputUserB = intval(htmlspecialchars($_POST["BONUS_CNT"]));
                        if (!empty($inputUserB) && $inputUserB != '' && $inputUserB != 0 && $inputUserB > 0) {
                           if ($inputUserB > $currentBonusBudget)
                              $inputUserB = intval($currentBonusBudget);
                           if ($inputUserB > $orderTotalSum)
                              $inputUserB = $orderTotalSum;
                           if ($inputUserB > $MAXpay)
                              $inputUserB = intval($MAXpay);
                        } else {
                           $inputUserB = ($MAXpay > $currentBonusBudget) ? $currentBonusBudget : intval($MAXpay);
                        }
                     } else {
                        $inputUserB = ($MAXpay > $currentBonusBudget) ? $currentBonusBudget : intval($MAXpay);
                     }
                  }
               }
            }
            $_SESSION['BONUSFORORDER'] = $bonusToPay ? 0 : $this->GetCartOrderBonus('ORDER');
            $arResult['PAYFROMPAY_FORMATED'] = $inputUserB;
            $arResult['PAYFROMPAY'] = $inputUserB;
            $arResult['PAYED_FROM_USERACCOUNT'] = $inputUser;
            $arResult['MINUS'] = false;
            $summa = $arResult['ORDER_PRICE'] + $arResult['DELIVERY_PRICE'] + $arResult['TAX_PRICE']; // - $arResult['DISCOUNT_PRICE'];
            if ($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] && !$arResult['USER_VALS']['PAY_BONUS_ACCOUNT']) {
               $ac_summa = intval($arResult['PAYED_FROM_ACCOUNT_FORMATED']);
               $summa -= $ac_summa;
               $arResult['MINUS'] = true;
            }
            if ($arResult['USER_VALS']['PAY_BONUS_ACCOUNT'] == 'Y') {
               $b_summa = intval($arResult['PAYFROMPAY']);
               $summa -= $b_summa;
               $GLOBALS['BONUS_PAY'] = $b_summa;
               $arResult['PAYED_FROM_BONUS_FORMATED'] = $this->declOfNum($arResult['PAYFROMPAY'], ($option['OPTION']['SUFIX'] == 'NAME' ? $option['OPTION']['NAME'] : array("", "", "")), $this->ModuleCurrency());
               $arResult['MINUS'] = true;
            }
            if ($arResult['USER_VALS']['PAY_CURRENT_ACCOUNT'] == 'Y') {
               $a_summa = intval($arResult['PAYED_FROM_USERACCOUNT']);
               $summa -= $a_summa;
               $GLOBALS['ACCOUNT_PAY'] = $a_summa;
               $arResult['PAYED_FROM_ACCOUNT_FORMATED'] = FormatCurrency($a_summa, $arResult['BASE_LANG_CURRENCY']);
               $arResult['MINUS'] = true;
            }
            $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = SaleFormatCurrency($summa, $arResult['BASE_LANG_CURRENCY']);
            $arResult['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'] = $arResult['JS_DATA']['TOTAL']['ORDER_TOTAL_LEFT_TO_PAY_FORMATED'];
         }
      }
   }

   public function GetBonusPayProfile($arFields) {
      $bonuspayprofile = $this->GetOptions($this->SITE_ID, "BONUSPAYPROFILETYPE");
      $TYPE = explode(",", $bonuspayprofile['OPTION']);
      $return = array();

      $res = $this->GetProfiles(array('ACTIVE' => 'Y', 'TYPE' => $TYPE, 'SITE' => $this->SITE_ID));
      if ($this->CheckArray($res)) {
         foreach ($res as $prof) {
            if (\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isAdminSection()) {
               $check = ($prof['ISADMIN'] == 'Y');
            } else
               $check = true;
            if ($check) {
               $Filter = call_user_func_array(array($this->INSTALL_PROFILE[$prof['TYPE']], "GetRules"), array($this->FUNC_GETRULES[$prof['TYPE']], $prof['ID'], $this->CheckSerialize($prof['FILTER']), $arFields));
               if ($Filter) {
                  $return[] = $prof;
               }
            }
         }
      }
      return $return;
   }

   public function GetOrderCountByUser($USER_ID, $order_id = null) {
      $payed = $this->GetOptions($this->SITE_ID, 'CHECKPAYORDER');
      $payed = $payed['OPTION'];
      if ($payed == '')
         $payed = 'N';
      if ($USER_ID) {
         $filter = [
             'USER_ID' => $USER_ID,
             'PAYED' => $payed,
         ];
         if (!is_null($order_id)) {
            $filter['!ID'] = $order_id;
         }

         if ($this > $this->isD7()) {
            $order = \Bitrix\Sale\Order::getList(array(
                        'filter' => $filter
                    ))->getSelectedRowsCount();
            return $order;
         } else {
            $order = \CSaleOrder::GetList(array('ID' => 'ASC'), $filter)->SelectedRowsCount();
            return $order;
         }
      }
   }

   public function OrderPaid($ORDER_ID) {
      $return = 'N';
      $filter = array('ORDER_ID' => $ORDER_ID);
      if ($this > $this->isD7()) {
         $order = \Bitrix\Sale\Order::load($ORDER_ID);
         $return = $order->isPaid();
      } else {
         $order = \CSaleOrder::GetByID($ORDER_ID);
         $return = ($order['PAYED'] == 'Y');
      }

      return $return;
   }

}

class Filter {

   private $out = array();

   public function __construct($input) {
      $this->out = $input;
   }

   public function byField($field, $pattern) {
      $temp = array();
      foreach ($this->out as $value) { {
            if ($this->isMatch($value[$field], $pattern)) {
               $temp[] = $value;
            }
         }
      }
      $this->out = $temp;
      return $this;
   }

   public function getResult() {
      return $this->out;
   }

   private function isMatch($value, $pattern) {
      if (is_array($pattern)) {
         return in_array(trim($value), $pattern);
      } else
         return trim($value) == trim($pattern);
   }

}

final class FileReader {

   protected $handler = null;
   protected $fbuffer = array();

   public function __construct($filename) {
      if (!($this->handler = fopen($filename, "rb")))
         throw new \Bitrix\Main\SystemException("Cannot open the file");
   }

   public function Read($count_line = 10) {
      if (!$this->handler)
         throw new \Bitrix\Main\SystemException("Invalid file pointer");

      while (!feof($this->handler)) {
         $this->fbuffer[] = fgets($this->handler);
         $count_line--;
         if ($count_line == 0)
            break;
      }

      return $this->fbuffer;
   }

   public function ReturnCount($pp) {
      return sizeof(file($pp));
   }

   public function SetOffset($line = 0) {
      if (!$this->handler)
         throw new \Bitrix\Main\SystemException("Invalid file pointer");

      while (!feof($this->handler) && $line--) {
         fgets($this->handler);
      }
   }

}
