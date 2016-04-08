$('#ribbon').append(
    '<div class="demo"><span id="demo-setting"><i class="fa fa-cog txt-color-blueDark"></i></span> <form><legend class="no-padding margin-bottom-10">Personaliza tu App</legend><section><label><input name="subscription" id="smart-fixed-nav" type="checkbox" class="checkbox style-0"><span>Header Estatico</span></label><label><input type="checkbox" name="terms" id="smart-fixed-ribbon" class="checkbox style-0"><span>Nav. Estatico</span></label><label><input type="checkbox" name="terms" id="smart-fixed-navigation" class="checkbox style-0"><span>Menu Estatico</span></label><label><input type="checkbox" name="terms" id="smart-fixed-container" class="checkbox style-0"><span>Contraer<b>.Panel</b> <div class="font-xs text-right">(no ajustable)</div></span></label><label style="display:none;"><input type="checkbox" name="terms" id="smart-rtl" class="checkbox style-0"><span>Right to left <b>(rtl)</b></span></label> <span id="smart-bgimages"></span></section><section><h6 class="margin-top-10 semi-bold margin-bottom-5">Resetear</h6><a href="javascript:void(0);" class="btn btn-xs btn-block btn-primary" id="reset-smart-widget"><i class="fa fa-refresh"></i> Resetear</a></section> <h6 class="margin-top-10 semi-bold margin-bottom-5">Temas</h6><section id="smart-styles"><a href="javascript:void(0);" id="smart-style-0" class="btn btn-block btn-xs txt-color-white margin-right-5" style="background-color:#4E463F;"><i class="fa fa-check fa-fw" id="skin-checked"></i>Negro - Dark</a><a href="javascript:void(0);" id="smart-style-1"  class="btn btn-block btn-xs txt-color-white" style="background:#3A4558;"> Azul Indigo</a><a href="javascript:void(0);" id="smart-style-2" class="btn btn-xs btn-block txt-color-darken margin-top-5" style="background:#fff;"> Blanco - light</a><a href="javascript:void(0);" id="smart-style-3" class="btn btn-xs btn-block txt-color-white margin-top-5" style="background:#f78c40"> Naranja</a></section></form> </div>'
)

var smartbgimage ="";
$("#smart-bgimages").fadeOut();

$('#demo-setting').click(function () {
    $('#ribbon .demo').toggleClass('activate');
})

/*
 * FIXED HEADER
 */
if(localStorage.getItem('smart-fixed-nav')==1){
    $.root_.toggleClass("fixed-header");
    $('input[type="checkbox"]#smart-fixed-nav').prop('checked',true);
}

$('input[type="checkbox"]#smart-fixed-nav')
    .click(function () {
        if ($(this)
            .is(':checked')) {
                localStorage.setItem('smart-fixed-nav',1);
                //checked
                $.root_.addClass("fixed-header");
                nav_page_height();
            }else{
                localStorage.setItem('smart-fixed-nav',0);
                $.root_.removeClass("fixed-header");
            }
    });

/*
 * FIXED RIBBON
 */
$('input[type="checkbox"]#smart-fixed-ribbon').click(function(){
    if ($(this)
        .is(':checked')) {
        //checked
        $('input[type="checkbox"]#smart-fixed-nav').prop('checked', true);

        $.root_.addClass("fixed-header");
        $.root_.addClass("fixed-ribbon");

        $('input[type="checkbox"]#smart-fixed-container').prop('checked', false);
        $.root_.removeClass("container");

    } else {
        //unchecked
        $('input[type="checkbox"]#smart-fixed-navigation').prop('checked', false);
        $.root_.removeClass("fixed-ribbon");
        $.root_.removeClass("fixed-navigation");
        $.root_.removeClass("container");
    }
});


/*
 * FIXED NAV
 */

$('input[type="checkbox"]#smart-fixed-navigation').click(function (){
    if ($(this).is(':checked')) {
        //checked
        $('input[type="checkbox"]#smart-fixed-nav').prop('checked', true);
        $('input[type="checkbox"]#smart-fixed-ribbon').prop('checked', true);

        //apply
        $.root_.addClass("fixed-header");
        $.root_.addClass("fixed-ribbon");
        $.root_.addClass("fixed-navigation");

        $('input[type="checkbox"]#smart-fixed-container').prop('checked', false);
        $.root_.removeClass("container");

    } else {
        //unchecked
        $.root_.addClass("fixed-navigation");
    }
});

/*
 * RTL SUPPORT
 */
$('input[type="checkbox"]#smart-rtl').click(function () {
    if ($(this).is(':checked')) {
        $.root_.addClass("smart-rtl");
    } else {
        $.root_.removeClass("smart-rtl");
    }
});


/*
 * INSIDE CONTAINER
 */

$('#smart-fixed-container').click(function () {
    if ($(this).is(':checked')) {
        //checked
		if($.root_==undefined){$.root_ = $('body');}
        $.root_.toggleClass("container");

        $('input[type="checkbox"]#smart-fixed-ribbon')
            .prop('checked', false);
        $.root_.removeClass("fixed-ribbon");

        $('input[type="checkbox"]#smart-fixed-navigation').prop('checked', false);
        $.root_.removeClass("fixed-navigation");

        if (smartbgimage) {
            $("#smart-bgimages")
                .append(smartbgimage)
                .fadeIn(1000);
            $("#smart-bgimages img")
                .bind("click", function () {
                    var $this = $(this);
                    var $html = $('html')
                    bgurl = ($this.data("htmlbg-url"));
                    $html.css("background-image", "url(" +
                        bgurl + ")");
                })

            smartbgimage = null;
        } else {
            $("#smart-bgimages")
                .fadeIn(1000);
        }

    localStorage.setItem('checked',1);
    } else {
        //unchecked
        $.root_.removeClass("container");
        $("#smart-bgimages").fadeOut();
        // console.log("container off");
        localStorage.setItem('checked',0);
    }
});

/*
 * REFRESH WIDGET
 */
$("#reset-smart-widget").bind("click", function () {
    $('#refresh').click();
    return false;
});

/*
 * STYLES
 */
$("#smart-styles > a").bind("click", function () {
    var $this = $(this);
    var $logo = $("#logo img");

    $.root_.removeClassPrefix('smart-style').addClass($this.attr("id"));
    localStorage.setItem('style',$this.attr("id"));

    $logo.attr('src', $this.data("skinlogo"));
    $("#smart-styles > a #skin-checked").remove();
    $this.prepend(
        "<i class='fa fa-check fa-fw' id='skin-checked'></i>"
    );
});

if(localStorage.getItem('checked')==1){ 
    //$('body').addClass("container"); 
    $('input[type="checkbox"]#smart-fixed-container').trigger('click');
}else{ 
    $('body').removeClass("container"); 
    $('input[type="checkbox"]#smart-fixed-container').attr('checked',false);
}

if(localStorage.getItem('style') != ''){ 
    $('body').removeClassPrefix('smart-style').addClass(localStorage.getItem('style'));
    $("#smart-styles > a #skin-checked").remove();
    $("#smart-styles > a").each(function(){;
        if($(this).attr('id')==localStorage.getItem('style')){$(this).prepend("<i class='fa fa-check fa-fw' id='skin-checked'></i>");}
    });
}        