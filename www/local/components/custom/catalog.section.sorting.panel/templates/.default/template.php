<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<? $this->setFrameMode(false); ?>
<div class="b-sorting-panel">
        
    <div class="b-alphabet b-sorting-el">
        <a class="<? if($_COOKIE['BIOKAMIN_SM_AlphabetSort']):?>active <? endif;?><? if($_COOKIE['BIOKAMIN_SM_AlphabetSort'] == 'desc'):?>desc<?else:?>asc<?endif?>" href="" title="По названию">
        	По названию
        </a>
    </div>
    <div class="b-price-sorting b-sorting-el">
        <a class="<? if(!$_COOKIE['BIOKAMIN_SM_AlphabetSort']):?>active <? endif;?><? if($_COOKIE['BIOKAMIN_SM_PriceSort'] == 'desc'):?>desc<?else:?>asc<?endif?>" href="" title="По цене">
        	По цене
        </a>
    </div>
</div>