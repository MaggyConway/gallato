<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?CVBCHBB::RefreshPayedFromAccount($arResult);?>
<div class="section">
	<script type="text/javascript">
		function changePaySystem(param)
		{
			//this is bigbonus module add--------------------------------------------------------
			if(param == 'bonus'){
				BX("PAY_BONUS_ACCOUNT").checked = !BX("PAY_BONUS_ACCOUNT").checked;
				BX.addClass(BX("PAY_BONUS_ACCOUNT_LABEL"), 'selected');
			}
			//-------------------------------------------------------------------------------
			if (BX("account_only") && BX("account_only").value == 'Y') // PAY_CURRENT_ACCOUNT checkbox should act as radio
			{
				if (param == 'account')
				{
					if (BX("PAY_CURRENT_ACCOUNT"))
					{
						BX("PAY_CURRENT_ACCOUNT").checked = true;
						BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
						BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');

						// deselect all other
						var el = document.getElementsByName("PAY_SYSTEM_ID");
						for(var i=0; i<el.length; i++)
							el[i].checked = false;
					}
				}
				else
				{
					BX("PAY_CURRENT_ACCOUNT").checked = false;
					BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
					BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
				}
			}
			else if (BX("account_only") && BX("account_only").value == 'N')
			{
				if (param == 'account')
				{
					if (BX("PAY_CURRENT_ACCOUNT"))
					{
						BX("PAY_CURRENT_ACCOUNT").checked = !BX("PAY_CURRENT_ACCOUNT").checked;

						if (BX("PAY_CURRENT_ACCOUNT").checked)
						{
							BX("PAY_CURRENT_ACCOUNT").setAttribute("checked", "checked");
							BX.addClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
						}
						else
						{
							BX("PAY_CURRENT_ACCOUNT").removeAttribute("checked");
							BX.removeClass(BX("PAY_CURRENT_ACCOUNT_LABEL"), 'selected');
						}
					}
				}
			}

			submitForm();
		}
	</script>
	<div class="bx_section">
		<h4><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></h4>
		<?
		// this is bigbonuse module replace code -------------------------------------------------------------------------------------------------------------
		if ($arResult['TYPEPAY']=="SYSTEM_PAY" || $arResult['TYPEPAY']=="SYSTEM_BIGBONUS_PAY" && $arResult['PAY_FROM_ACCOUNT1']=='Y')
       		{
			$accountOnly = ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N";
			?>
			<input type="hidden" id="account_only" value="<?=$accountOnly?>" />
			<div class="bx_block w100 vertical">
				<div class="bx_element">
					<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
					<label for="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT_LABEL" onclick="changePaySystem('account');" class="<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo "selected"?>">
						<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?>>
						<div class="bx_logotype">
							<span style="background-image:url(<?=$templateFolder?>/images/logo-default-ps.gif);"></span>
						</div>
						<div class="bx_description">
							<strong><?=GetMessage('VBCHBB_SALE_ORDER_AJAX_P6')?>&nbsp; <?=$arResult['ORDER_USER_PERCENT']?>)</strong>
							<p>
								<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"]?></b></div>
								<? if ($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"):?>
									<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT3")?></div>
								<? else:?>
									<div><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?></div>
								<? endif;?>
								<?if(!$arResult['USERINPUT']){?>
									<input type="hidden" name="ACCOUNT_CNT" value="<?=$arResult['PAYED_FROM_USERACCOUNT']?>"/>
								<?}?>
							</p>
						</div>
					</label>
					<?if($arResult['USERINPUT']){?>
						<div>
							<input type="text" id="account_pay" name="ACCOUNT_CNT" placeholder="<?=$arResult["PAYED_FROM_USERACCOUNT"]?>"
								   style="width:180px;" value="<?=$arResult['PAYED_FROM_USERACCOUNT']?>"/>
						</div>
					<?}?>

					<div class="clear"></div>
				</div>
			</div>
			<?
		}
		if($arResult['TYPEPAY']=="BIGBONUS_PAY" || $arResult['TYPEPAY']=="SYSTEM_BIGBONUS_PAY" && $arResult['PAY_FROM_BONUS']=="Y"){
			?>
			<div class="bx_block w100 vertical">
				<div class="bx_element">
					<input type="hidden" name="PAY_BONUS_ACCOUNT" value="N">
					<label for="PAY_BONUS_ACCOUNT" id="PAY_BONUS_ACCOUNT_LABEL" onclick="changePaySystem('bonus');" class="<?if($arResult["USER_VALS"]["PAY_BONUS_ACCOUNT"]=="Y") echo "selected"?>" style="width:100%">
						<input type="checkbox" name="PAY_BONUS_ACCOUNT" id="PAY_BONUS_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_BONUS_ACCOUNT"]=="Y") echo " checked=\"checked\"";?>>
						<div class="bx_logotype">
							<span style="background-image:url(<?=$templateFolder?>/images/bonus-ps.gif);"></span>
						</div>
						<div class="bx_description">
							<strong><?=GetMessage('VBCHBB_SALE_ORDER_AJAX_P0')?>&nbsp;<?=$arResult['ORDER_PAY_PERCENT']?>)</strong>
							<p>
								<div><?=GetMessage('VBCHBB_SALE_ORDER_AJAX_P1')?>&nbsp;<b><?=$arResult["CURRENT_BONUS_BUDGET_FORMATED"]?></b></div>
								<?if(!$arResult['BONUSINPUT']){?>
								<div><?=GetMessage('VBCHBB_SALE_ORDER_AJAX_P2')?>&nbsp;<?=$arResult['PAYFROMPAY_FORMATED']?></div>
									<input type="hidden" name="BONUS_CNT" value="<?=$arResult['PAYFROMPAY']?>"/>
								<?}?>
							</p>
						</div>
					</label>
					<?if($arResult['BONUSINPUT']){?>
						<div>
							<input type="text" id="bonus_pay" name="BONUS_CNT" placeholder="<?=$arResult["PAYFROMPAY"]?>"
								   	style="width:180px;" value="<?=$arResult['PAYFROMPAY']?>"/>
						</div>
					<?}?>
					<div class="clear"></div>
				</div>
			</div>
			<?
		}
		//----------------------------------------------------------------------------------------------------------------------------------------------------------
		uasort($arResult["PAY_SYSTEM"], "cmpBySort"); // resort arrays according to SORT value
		foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
		{
			if (strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) > 0 || intval($arPaySystem["PRICE"]) > 0)
			{
				if (count($arResult["PAY_SYSTEM"]) == 1)
				{
					?>
					<div class="bx_block w100 vertical">
						<div class="bx_element">
							<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
							<input type="radio"
								id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
								name="PAY_SYSTEM_ID"
								value="<?=$arPaySystem["ID"]?>"
								<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
								onclick="changePaySystem();"
								/>
							<label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
								<?
								if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
									$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
								else:
									$imgUrl = $templateFolder."/images/logo-default-ps.gif";
								endif;
								?>
								<div class="bx_logotype">
									<span style="background-image:url(<?=$imgUrl?>);"></span>
								</div>
								<div class="bx_description">
									<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
										<strong><?=$arPaySystem["PSA_NAME"];?></strong>
									<?endif;?>
									<p>
										<?
										if (intval($arPaySystem["PRICE"]) > 0)
											echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), GetMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
										else
											echo $arPaySystem["DESCRIPTION"];
										?>
									</p>
								</div>
							</label>
							<div class="clear"></div>
						</div>
					</div>
					<?
				}
				else // more than one
				{
				?>
					<div class="bx_block w100 vertical">
						<div class="bx_element">
							<input type="radio"
								id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
								name="PAY_SYSTEM_ID"
								value="<?=$arPaySystem["ID"]?>"
								<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
								onclick="changePaySystem();" />
							<label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
								<?
								if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
									$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
								else:
									$imgUrl = $templateFolder."/images/logo-default-ps.gif";
								endif;
								?>
								<div class="bx_logotype">
									<span style='background-image:url(<?=$imgUrl?>);'></span>
								</div>
								<div class="bx_description">
									<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
										<strong><?=$arPaySystem["PSA_NAME"];?></strong>
									<?endif;?>
									<p>
										<?
										if (intval($arPaySystem["PRICE"]) > 0)
											echo str_replace("#PAYSYSTEM_PRICE#", SaleFormatCurrency(roundEx($arPaySystem["PRICE"], SALE_VALUE_PRECISION), $arResult["BASE_LANG_CURRENCY"]), GetMessage("SOA_TEMPL_PAYSYSTEM_PRICE"));
										else
											echo $arPaySystem["DESCRIPTION"];
										?>
									</p>
								</div>
							</label>
							<div class="clear"></div>
						</div>
					</div>
				<?
				}
			}

			if (strlen(trim(str_replace("<br />", "", $arPaySystem["DESCRIPTION"]))) == 0 && intval($arPaySystem["PRICE"]) == 0)
			{
				if (count($arResult["PAY_SYSTEM"]) == 1)
				{
					?>
					<div class="bx_block horizontal">
						<div class="bx_element">
							<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
							<input type="radio"
								id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
								name="PAY_SYSTEM_ID"
								value="<?=$arPaySystem["ID"]?>"
								<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
								onclick="changePaySystem();"
								/>
							<label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
							<?
							if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
								$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
							else:
								$imgUrl = $templateFolder."/images/logo-default-ps.gif";
							endif;
							?>
							<div class="bx_logotype">
								<span style='background-image:url(<?=$imgUrl?>);'></span>
							</div>
							<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
								<div class="bx_description">
									<div class="clear"></div>
									<strong><?=$arPaySystem["PSA_NAME"];?></strong>
								</div>
							<?endif;?>
						</div>
					</div>
				<?
				}
				else // more than one
				{
				?>
					<div class="bx_block horizontal">
						<div class="bx_element">

							<input type="radio"
								id="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>"
								name="PAY_SYSTEM_ID"
								value="<?=$arPaySystem["ID"]?>"
								<?if ($arPaySystem["CHECKED"]=="Y" && !($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && $arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y")) echo " checked=\"checked\"";?>
								onclick="changePaySystem();" />

							<label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;changePaySystem();">
								<?
								if (count($arPaySystem["PSA_LOGOTIP"]) > 0):
									$imgUrl = $arPaySystem["PSA_LOGOTIP"]["SRC"];
								else:
									$imgUrl = $templateFolder."/images/logo-default-ps.gif";
								endif;
								?>
								<div class="bx_logotype">
									<span style='background-image:url(<?=$imgUrl?>);'></span>
								</div>
								<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
									<div class="bx_description">
										<div class="clear"></div>
										<strong>
											<?if ($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
												<?=$arPaySystem["PSA_NAME"];?>
											<?else:?>
												<?="&nbsp;"?>
											<?endif;?>
										</strong>
									</div>
								<?endif;?>

							</label>
						</div>
					</div>
				<?
				}
			}
		}
		?>
		<div style="clear: both;"></div>
	</div>
</div>