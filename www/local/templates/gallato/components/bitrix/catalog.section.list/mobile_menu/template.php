<!-- ФАЙЛ ГДЕ ССЫЛКИ КАТЕГОРИЙ ТОВАРОВ, ВЕРСТКА -->

<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
$this->setFrameMode(true);

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')); 

$intCurrentDepth = 1;
$boolFirst = true;

//echo "<pre>"; var_dump($arResult['SECTIONS']); echo "</pre>";

if (0 < $arResult["SECTIONS_COUNT"]) { ?>


<!-- mp-menu -->
<nav id="mp-menu" class="mp-menu">
	<div class="mp-level">
		<h2 class="icon icon-shop">Каталог</h2>
		<ul>
			<li class="sale">
				<a href="/sale/">SALE</a>
			</li>

<?
// <h2 class="icon icon-shop">'.$arSection["NAME"].'</h2>

foreach ($arResult['SECTIONS'] as &$arSection) {
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);

	if ($intCurrentDepth < $arSection['RELATIVE_DEPTH_LEVEL']) {
		if (0 < $intCurrentDepth)
			echo "\n",str_repeat("\t", $arSection['RELATIVE_DEPTH_LEVEL']),'<div class="mp-level"><a class="mp-back" href="#">Назад</a><ul>';

	} elseif ($intCurrentDepth == $arSection['RELATIVE_DEPTH_LEVEL']) {
		if (!$boolFirst)
			echo '</li>';
	}   else {

		while ($intCurrentDepth > $arSection['RELATIVE_DEPTH_LEVEL']) {
			echo '</li>',"\n",str_repeat("\t", $intCurrentDepth),'</ul>',"\n",str_repeat("\t", $intCurrentDepth-1);
			$intCurrentDepth--;
		}
		echo str_repeat("\t", $intCurrentDepth-1),'</li>';
	}

	echo (!$boolFirst ? "\n" : ''),str_repeat("\t", $arSection['RELATIVE_DEPTH_LEVEL']);
	?>

	<li>
		<a href="<? echo $arSection["SECTION_PAGE_URL"]; ?>">
			<? echo $arSection["NAME"];?>
		</a>

		<?
		$intCurrentDepth = $arSection['RELATIVE_DEPTH_LEVEL'];
		$boolFirst = false;
	}
	unset($arSection);
	while ($intCurrentDepth > 1) {
		echo '</li>',"\n",str_repeat("\t", $intCurrentDepth),'</ul>',"\n",str_repeat("\t", $intCurrentDepth-1);
		$intCurrentDepth--;
	}
	if ($intCurrentDepth > 0) {
		echo '</li>',"\n";
	}
?>
		</ul>

	</div>
</nav>
<!-- /mp-menu -->

<? } ?>

<!-- <li><a href="/wholesalers/">оптовикам</a></li>
<li><a href="/franchise/">франшиза</a></li>
