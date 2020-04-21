<!-- РЕЗУЛЬТАТЫ ПОИСКА -->
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>

<div class="wrapper">
<div class="search_page">

	<form action="" method="get" class="search_section">

	<?if($arParams["USE_SUGGEST"] === "Y"):
		if(strlen($arResult["REQUEST"]["~QUERY"]) && is_object($arResult["NAV_RESULT"]))
		{
			$arResult["FILTER_MD5"] = $arResult["NAV_RESULT"]->GetFilterMD5();
			$obSearchSuggest = new CSearchSuggest($arResult["FILTER_MD5"], $arResult["REQUEST"]["~QUERY"]);
			$obSearchSuggest->SetResultCount($arResult["NAV_RESULT"]->NavRecordCount);
		}
		?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:search.suggest.input",
			"",
			array(
				"NAME" => "q",
				"VALUE" => $arResult["REQUEST"]["~QUERY"],
				"INPUT_SIZE" => 40,
				"DROPDOWN_SIZE" => 10,
				"FILTER_MD5" => $arResult["FILTER_MD5"],
			),
			$component, array("HIDE_ICONS" => "Y")
		);?>
	<?else:?>
		<input type="text" id="search_page_input" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" placeholder="Найти товар" size="40" />
	<?endif;?>

		<button type="submit" class="btn"></button>

		<input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />

	</form>





	<div class="search_error">
		<?if(isset($arResult["REQUEST"]["ORIGINAL_QUERY"])):
			?>
			<div class="search-language-guess">
				<?echo GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#"=>'<a href="'.$arResult["ORIGINAL_QUERY_URL"].'">'.$arResult["REQUEST"]["ORIGINAL_QUERY"].'</a>'))?>
			</div>
		<?endif;?>


		<?if($arResult["ERROR_CODE"]!=0):?>

			<p><?=GetMessage("SEARCH_ERROR")?></p>
			<?ShowError($arResult["ERROR_TEXT"]);?>
			<p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p>
			<br /><br />
			<p><?=GetMessage("SEARCH_SINTAX")?><br /><b><?=GetMessage("SEARCH_LOGIC")?></b></p>

			<table border="0" cellpadding="5">
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_OPERATOR")?></td><td valign="top"><?=GetMessage("SEARCH_SYNONIM")?></td>
					<td><?=GetMessage("SEARCH_DESCRIPTION")?></td>
				</tr>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_AND")?></td><td valign="top">and, &amp;, +</td>
					<td><?=GetMessage("SEARCH_AND_ALT")?></td>
				</tr>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_OR")?></td><td valign="top">or, |</td>
					<td><?=GetMessage("SEARCH_OR_ALT")?></td>
				</tr>
				<tr>
					<td align="center" valign="top"><?=GetMessage("SEARCH_NOT")?></td><td valign="top">not, ~</td>
					<td><?=GetMessage("SEARCH_NOT_ALT")?></td>
				</tr>
				<tr>
					<td align="center" valign="top">( )</td>
					<td valign="top">&nbsp;</td>
					<td><?=GetMessage("SEARCH_BRACKETS_ALT")?></td>
				</tr>
			</table>
		<?endif;?>
	</div>





<?if(count($arResult["SEARCH"])>0):?>

<div class="products_grid">

	<?foreach($arResult["SEARCH"] as $arItem):?>

		<!-- <a href="<?/*echo $arItem["URL"]?>" class="item_title"><?echo $arItem["TITLE_FORMATED"]*/?></a> -->
		
		<? 
		global $USER;

		$grab = GetIBlockElement($arItem["ITEM_ID"]);

		$image_prw = CFile::GetPath($grab["PREVIEW_PICTURE"]);


if ($grab["NAME"] !== NULL) { ?>

			<div class="product_item">
				<div class="item__top_block">
					<a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="photo" style="background: #fff url(<?=$image_prw?>) no-repeat center center;"></a>
		<?
		if ($USER->IsAuthorized()) { 

			global $fav_products;
			$in_wish_list = false;

			if (in_array($grab["ID"], $fav_products)) {
			    $in_wish_list = true;
			}
		?>

			<div class="item__like <?=($in_wish_list) ? "active" : ""  ?>" data-product-id="<?=$grab["ID"]?>"></div>

		<?}?>
					<a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="item__btn">открыть</a>

					<? if ($grab["PROPERTIES"]["SALE"]["VALUE"] == "Y") { ?>

						<div class="item_sale_sticker">SALE</div>

					<? } ?>
				</div>

	<div class="prices">


		<?
		$arGroupAvalaibleOpt = array(7);
		$arGroupAvalaibleRozn = array(6);
		$arGroupAvalaibleAdmin = array(1);
		$arGroups = CUser::GetUserGroup($USER->GetID());
		$result_intersect_opt = array_intersect($arGroupAvalaibleOpt, $arGroups);
		$result_intersect_rozn = array_intersect($arGroupAvalaibleRozn, $arGroups);
		$result_intersect_admin = array_intersect($arGroupAvalaibleAdmin, $arGroups);

		if ($USER->IsAuthorized()) { 
			if(!empty($result_intersect_opt)) {

				$opt_price = CPrice::GetList(
			        array(),
			        array(
			                "PRODUCT_ID" => $arItem["ITEM_ID"],
			                "CATALOG_GROUP_ID" => 1
			            )
				);

				if ($opt_res = $opt_price->Fetch()) {
				    //var_dump($opt_res["PRICE"]); 
				    ?>
				    <a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="item__price">
					<?=$opt_res["PRICE"] ?>&nbsp;Р</a>
					<?
				}
				else {
				    echo "Цена не найдена!";
				}

			} elseif (!empty($result_intersect_rozn)) {
				
				$rozn_price = CPrice::GetList(
		        array(),
		        array(
		                "PRODUCT_ID" => $arItem["ITEM_ID"],
		                "CATALOG_GROUP_ID" => 2
		            )
				);
				if ($rozn_res = $rozn_price->Fetch()) {
				    //var_dump($rozn_res["PRICE"]);
				    ?>
				    <a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="item__price">
					<?=$rozn_res["PRICE"] ?>&nbsp;Р</a>
					<?
				}
				else {
				    echo "Цена не найдена!";
				}
					
			} elseif (!empty($result_intersect_admin)) {

				$rozn_price = CPrice::GetList(
			    array(),
			    array(
			            "PRODUCT_ID" => $arItem["ITEM_ID"],
			            "CATALOG_GROUP_ID" => 2
			        )
				);
				if ($rozn_res = $rozn_price->Fetch()) {
				    //var_dump($rozn_res["PRICE"]);
				    ?>
				    <a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="item__price">
					<?=$rozn_res["PRICE"] ?>&nbsp;Р</a>
					<?
				}
				else {
				    echo "Цена не найдена!";
				}

			}

		} elseif (!$USER->IsAuthorized()) {
			
			$rozn_price = CPrice::GetList(
		    array(),
		    array(
		            "PRODUCT_ID" => $arItem["ITEM_ID"],
		            "CATALOG_GROUP_ID" => 2
		        )
			);
			if ($rozn_res = $rozn_price->Fetch()) {
			    //var_dump($rozn_res["PRICE"]);
			    ?>
			    <a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="item__price">
				<?=$rozn_res["PRICE"] ?>&nbsp;Р</a>
				<?
			}
			else {
			    echo "Цена не найдена!";
			}
		}?>


		<div class="old_price">
			<p><?=$grab["PROPERTIES"]["OLD_PRICE"]["VALUE"]?></p><span>Р</span>
		</div>


	</div>

	<a href="<?=$grab["DETAIL_PAGE_URL"]?>" class="item__label"><?=$grab["NAME"]?></a>
</div>

<?}?>
		
	<?endforeach;?>
</div><!-- /products_grid -->

<?elseif( count($arResult["SEARCH"])<=0 ):?>
	<?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
<?endif;?>

</div><!-- /search_page -->
</div><!-- /wrapper -->