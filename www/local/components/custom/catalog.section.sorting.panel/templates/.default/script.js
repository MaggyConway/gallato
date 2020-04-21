jQuery(document).ready(function($) {
    sectionList();
    sectionAlphabetSort();
    sectionPriceSort();
    sectionCountSort();
    //$.removeCookie('BIOKAMIN_SM_AlphabetSort', { path: '/' });
});
BX.addCustomEvent('onAjaxSuccess', function(){
    //Вид списка товаров
    sectionList();
    sectionAlphabetSort();
    sectionPriceSort();
    sectionCountSort();
});
function sectionList() {
    //Вид списка товаров
    $('.b-sorting-panel .b-grid').on('click', function (e) {
        e.preventDefault;
        //$.cookie('SectionView', 'grid', { expires: 90, path: '/' });
        BX.setCookie('BIOKAMIN_SM_SectionView', 'grid', {expires: 90});
    });
    $('.b-sorting-panel .b-list').on('click', function (e) {
        e.preventDefault;
        //$.cookie('SectionView', 'list', { expires: 90, path: '/' });
        BX.setCookie('BIOKAMIN_SM_SectionView', 'list', {expires: 90});
    });
}
function sectionAlphabetSort() {
    //Сортировка по алфавиту
    $('.b-alphabet .asc').on('click', function (e) {
        e.preventDefault;
        /*$.cookie('AlphabetSort', 'desc', { expires: 90, path: '/' });
        $.cookie('SortName', 'alph', { expires: 90, path: '/' });*/
        //$.removeCookie('BIOKAMIN_SM_PriceSort', { path: '/' });
        BX.setCookie('BIOKAMIN_SM_AlphabetSort', 'desc', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_SortName', 'alph', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_PriceSort', 'alph', {expires: -90});
    });
    $('.b-alphabet .desc').on('click', function (e) {
        e.preventDefault;
        /*$.cookie('AlphabetSort', 'asc', { expires: 90, path: '/' });
        $.cookie('SortName', 'alph', { expires: 90, path: '/' });*/
        //$.removeCookie('BIOKAMIN_SM_PriceSort', { path: '/' });
        BX.setCookie('BIOKAMIN_SM_AlphabetSort', 'asc', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_SortName', 'alph', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_PriceSort', 'alph', {expires: -90});
    });
}
function sectionPriceSort() {

    //Сортировка по цене
    $('.b-price-sorting .asc').on('click', function (e) {
        e.preventDefault;
        /*$.cookie('PriceSort', 'desc', { expires: 90, path: '/' });
        $.cookie('SortName', 'price', { expires: 90, path: '/' });*/
        //$.removeCookie('BIOKAMIN_SM_AlphabetSort', { path: '/' });
        BX.setCookie('BIOKAMIN_SM_PriceSort', 'desc', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_SortName', 'price', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_AlphabetSort', 'price', {expires: -90});

    });
    $('.b-price-sorting .desc').on('click', function (e) {
        
        e.preventDefault;
        /*$.cookie('PriceSort', 'asc', { expires: 90, path: '/' });
        $.cookie('SortName', 'price', { expires: 90, path: '/' });*/
        //$.removeCookie('BIOKAMIN_SM_AlphabetSort', { path: '/' });
        BX.setCookie('BIOKAMIN_SM_PriceSort', 'asc', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_SortName', 'price', {expires: 90});
        BX.setCookie('BIOKAMIN_SM_AlphabetSort', 'price', {expires: -90});
    });
}
function sectionCountSort() {
    //Количество элементов на странице
    var count = "";
    $('.b-sorting-panel .b-sorting-el .b-sorting-el-name').on('click', function () {
        $(this).parent().find('.b-sorting-el-dropdown').stop().slideToggle();
    });
    $('.b-element-count .b-sorting-el-dropdown li').each(function () {
        var that = $(this);
        $(this).on('click', function () {
            count = $(this).text();
            $('.b-sorting-el-name span').text(count);
            //$.cookie('CatalogElementCount', count, { expires: 90, path: '/' });
            BX.setCookie('BIOKAMIN_SM_CatalogElementCount', count, {expires: 90});
            $('.b-sorting-panel .b-sorting-el-dropdown').hide();
            location.reload();
        })
    })
}
