function t142_checkSize(recid){var el=$("#rec"+recid).find(".t142__submit");if(el.length){var btnheight=el.height()+5;var textheight=el[0].scrollHeight;if(btnheight<textheight){var btntext=el.text();el.addClass("t142__submit-overflowed");el.html("<span class=\"t142__text\">"+btntext+"</span>")}}}
function t228_highlight(){var url=window.location.href;var pathname=window.location.pathname;if(url.substr(url.length-1)=="index.html"){url=url.slice(0,-1)}
if(pathname.substr(pathname.length-1)=="index.html"){pathname=pathname.slice(0,-1)}
if(pathname.charAt(0)=="index.html"){pathname=pathname.slice(1)}
if(pathname==""){pathname="/"}
$(".t228__list_item a[href='"+url+"']").addClass("t-active");$(".t228__list_item a[href='"+url+"/']").addClass("t-active");$(".t228__list_item a[href='"+pathname+"']").addClass("t-active");$(".t228__list_item a[href='/"+pathname+"']").addClass("t-active");$(".t228__list_item a[href='"+pathname+"/']").addClass("t-active");$(".t228__list_item a[href='/"+pathname+"/']").addClass("t-active")}
function t228_checkAnchorLinks(recid){if($(window).width()>=960){var t228_navLinks=$("#rec"+recid+" .t228__list_item a:not(.tooltipstered)[href*='#']");if(t228_navLinks.length>0){setTimeout(function(){t228_catchScroll(t228_navLinks)},500)}}}
function t228_catchScroll(t228_navLinks){var t228_clickedSectionId=null,t228_sections=new Array(),t228_sectionIdTonavigationLink=[],t228_interval=100,t228_lastCall,t228_timeoutId;t228_navLinks=$(t228_navLinks.get().reverse());t228_navLinks.each(function(){var t228_cursection=t228_getSectionByHref($(this));if(typeof t228_cursection.attr("id")!="undefined"){t228_sections.push(t228_cursection)}
t228_sectionIdTonavigationLink[t228_cursection.attr("id")]=$(this)});t228_updateSectionsOffsets(t228_sections);t228_sections.sort(function(a,b){return b.attr("data-offset-top")-a.attr("data-offset-top")});$(window).bind('resize',t_throttle(function(){t228_updateSectionsOffsets(t228_sections)},200));$('.t228').bind('displayChanged',function(){t228_updateSectionsOffsets(t228_sections)});setInterval(function(){t228_updateSectionsOffsets(t228_sections)},5000);t228_highlightNavLinks(t228_navLinks,t228_sections,t228_sectionIdTonavigationLink,t228_clickedSectionId);t228_navLinks.click(function(){var t228_clickedSection=t228_getSectionByHref($(this));if(!$(this).hasClass("tooltipstered")&&typeof t228_clickedSection.attr("id")!="undefined"){t228_navLinks.removeClass('t-active');$(this).addClass('t-active');t228_clickedSectionId=t228_getSectionByHref($(this)).attr("id")}});$(window).scroll(function(){var t228_now=new Date().getTime();if(t228_lastCall&&t228_now<(t228_lastCall+t228_interval)){clearTimeout(t228_timeoutId);t228_timeoutId=setTimeout(function(){t228_lastCall=t228_now;t228_clickedSectionId=t228_highlightNavLinks(t228_navLinks,t228_sections,t228_sectionIdTonavigationLink,t228_clickedSectionId)},t228_interval-(t228_now-t228_lastCall))}else{t228_lastCall=t228_now;t228_clickedSectionId=t228_highlightNavLinks(t228_navLinks,t228_sections,t228_sectionIdTonavigationLink,t228_clickedSectionId)}})}
function t228_updateSectionsOffsets(sections){$(sections).each(function(){var t228_curSection=$(this);t228_curSection.attr("data-offset-top",t228_curSection.offset().top)})}
function t228_getSectionByHref(curlink){var t228_curLinkValue=curlink.attr('href').replace(/\s+/g,'').replace(/.*#/,'');if(curlink.is('[href*="#rec"]')){return $(".r[id='"+t228_curLinkValue+"']")}else{return $(".r[data-record-type='215']").has("a[name='"+t228_curLinkValue+"']")}}
function t228_highlightNavLinks(t228_navLinks,t228_sections,t228_sectionIdTonavigationLink,t228_clickedSectionId){var t228_scrollPosition=$(window).scrollTop(),t228_valueToReturn=t228_clickedSectionId;if(t228_sections.length!=0&&t228_clickedSectionId==null&&t228_sections[t228_sections.length-1].attr("data-offset-top")>(t228_scrollPosition+300)){t228_navLinks.removeClass('t-active');return null}
$(t228_sections).each(function(e){var t228_curSection=$(this),t228_sectionTop=t228_curSection.attr("data-offset-top"),t228_id=t228_curSection.attr('id'),t228_navLink=t228_sectionIdTonavigationLink[t228_id];if(((t228_scrollPosition+300)>=t228_sectionTop)||(t228_sections[0].attr("id")==t228_id&&t228_scrollPosition>=$(document).height()-$(window).height())){if(t228_clickedSectionId==null&&!t228_navLink.hasClass('t-active')){t228_navLinks.removeClass('t-active');t228_navLink.addClass('t-active');t228_valueToReturn=null}else{if(t228_clickedSectionId!=null&&t228_id==t228_clickedSectionId){t228_valueToReturn=null}}
return!1}});return t228_valueToReturn}
function t228_setPath(){}
function t228_setWidth(recid){var window_width=$(window).width();if(window_width>980){$(".t228").each(function(){var el=$(this);var left_exist=el.find('.t228__leftcontainer').length;var left_w=el.find('.t228__leftcontainer').outerWidth(!0);var max_w=left_w;var right_exist=el.find('.t228__rightcontainer').length;var right_w=el.find('.t228__rightcontainer').outerWidth(!0);var items_align=el.attr('data-menu-items-align');if(left_w<right_w)max_w=right_w;max_w=Math.ceil(max_w);var center_w=0;el.find('.t228__centercontainer').find('li').each(function(){center_w+=$(this).outerWidth(!0)});var padd_w=40;var maincontainer_width=el.find(".t228__maincontainer").outerWidth();if(maincontainer_width-max_w*2-padd_w*2>center_w+20){if(items_align=="center"||typeof items_align==="undefined"){el.find(".t228__leftside").css("min-width",max_w+"px");el.find(".t228__rightside").css("min-width",max_w+"px");el.find(".t228__list").removeClass("t228__list_hidden")}}else{el.find(".t228__leftside").css("min-width","");el.find(".t228__rightside").css("min-width","")}})}}
function t228_setBg(recid){var window_width=$(window).width();if(window_width>980){$(".t228").each(function(){var el=$(this);if(el.attr('data-bgcolor-setbyscript')=="yes"){var bgcolor=el.attr("data-bgcolor-rgba");el.css("background-color",bgcolor)}})}else{$(".t228").each(function(){var el=$(this);var bgcolor=el.attr("data-bgcolor-hex");el.css("background-color",bgcolor);el.attr("data-bgcolor-setbyscript","yes")})}}
function t228_appearMenu(recid){var window_width=$(window).width();if(window_width>980){$(".t228").each(function(){var el=$(this);var appearoffset=el.attr("data-appearoffset");if(appearoffset!=""){if(appearoffset.indexOf('vh')>-1){appearoffset=Math.floor((window.innerHeight*(parseInt(appearoffset)/100)))}
appearoffset=parseInt(appearoffset,10);if($(window).scrollTop()>=appearoffset){if(el.css('visibility')=='hidden'){el.finish();el.css("top","-50px");el.css("visibility","visible");var topoffset=el.data('top-offset');if(topoffset&&parseInt(topoffset)>0){el.animate({"opacity":"1","top":topoffset+"px"},200,function(){})}else{el.animate({"opacity":"1","top":"0px"},200,function(){})}}}else{el.stop();el.css("visibility","hidden");el.css("opacity","0")}}})}}
function t228_changebgopacitymenu(recid){var window_width=$(window).width();if(window_width>980){$(".t228").each(function(){var el=$(this);var bgcolor=el.attr("data-bgcolor-rgba");var bgcolor_afterscroll=el.attr("data-bgcolor-rgba-afterscroll");var bgopacityone=el.attr("data-bgopacity");var bgopacitytwo=el.attr("data-bgopacity-two");var menushadow=el.attr("data-menushadow");if(menushadow=='100'){var menushadowvalue=menushadow}else{var menushadowvalue='0.'+menushadow}
if($(window).scrollTop()>20){el.css("background-color",bgcolor_afterscroll);if(bgopacitytwo=='0'||(typeof menushadow=="undefined"&&menushadow==!1)){el.css("box-shadow","none")}else{el.css("box-shadow","0px 1px 3px rgba(0,0,0,"+menushadowvalue+")")}}else{el.css("background-color",bgcolor);if(bgopacityone=='0.0'||(typeof menushadow=="undefined"&&menushadow==!1)){el.css("box-shadow","none")}else{el.css("box-shadow","0px 1px 3px rgba(0,0,0,"+menushadowvalue+")")}}})}}
function t228_createMobileMenu(recid){var window_width=$(window).width(),el=$("#rec"+recid),menu=el.find(".t228"),burger=el.find(".t228__mobile");burger.click(function(e){menu.fadeToggle(300);$(this).toggleClass("t228_opened")})
$(window).bind('resize',t_throttle(function(){window_width=$(window).width();if(window_width>980){menu.fadeIn(0)}},200))}
function t270_scroll(hash,offset){var $root=$('html, body');var target="";try{target=$(hash)}catch(event){console.log("Exception t270: "+event.message);return!0}
if(target.length==0){target=$('a[name="'+hash.substr(1)+'"]');if(target.length==0){return!0}}
$root.animate({scrollTop:target.offset().top-offset},500,function(){if(history.pushState){history.pushState(null,null,hash)}else{window.location.hash=hash}});return!0}
function t347_setHeight(recid){var el=$('#rec'+recid);var div=el.find(".t347__table");var height=div.width()*0.5625;div.height(height)}
window.t347showvideo=function(recid){$(document).ready(function(){var el=$('#rec'+recid);var videourl='';var youtubeid=$("#rec"+recid+" .t347__video-container").attr('data-content-popup-video-url-youtube');if(youtubeid>''){videourl='https://www.youtube.com/embed/'+youtubeid}
$("#rec"+recid+" .t347__video-container").removeClass("t347__hidden");$("#rec"+recid+" .t347__video-carier").html("<iframe id=\"youtubeiframe"+recid+"\" class=\"t347__iframe\" width=\"100%\" height=\"100%\" src=\""+videourl+"?autoplay=1&rel=0\" frameborder=\"0\" allowfullscreen></iframe>")})}
window.t347hidevideo=function(recid){$(document).ready(function(){$("#rec"+recid+" .t347__video-container").addClass("t347__hidden");$("#rec"+recid+" .t347__video-carier").html("")})}
function t504_unifyHeights(recid){$('#rec'+recid+' .t504 .t-container').each(function(){var t504__highestBox=0;$('.t504__col',this).each(function(){var t504__curcol=$(this);var t504__curcolchild=t504__curcol.find('.t504__col-wrapper');if(t504__curcol.height()<t504__curcolchild.height())t504__curcol.height(t504__curcolchild.height());if(t504__curcol.height()>t504__highestBox)t504__highestBox=t504__curcol.height()});if($(window).width()>=960){$('.t504__col',this).css('height',t504__highestBox)}else{$('.t504__col',this).css('height',"auto")}})};function t544_setHeight(recid){var el=$('#rec'+recid);var sizer=el.find('.t544__sizer');var height=sizer.height();var width=sizer.width();var ratio=width/height;var imgwrapper=el.find(".t544__blockimg, .t544__textwrapper");var imgwidth=imgwrapper.width();if(height!=$(window).height()){imgwrapper.css({'height':((imgwidth/ratio)+'px')})}}
function t599_init(recid){var el=$('#rec'+recid);if(el.find('.t599__title').length){t599_equalHeight(el.find('.t599__title'))}
if(el.find('.t599__descr').length){t599_equalHeight(el.find('.t599__descr'))}
if(el.find('.t599__price').length){t599_equalHeight(el.find('.t599__price'))}
if(el.find('.t599__subtitle').length){t599_equalHeight(el.find('.t599__subtitle'))}};function t599_equalHeight(element){var highestBox=0;element.css('height','');element.each(function(){if($(this).height()>highestBox)highestBox=$(this).height()});if($(window).width()>=960){element.css('height',highestBox)}else{element.css('height','')}}
function t702_initPopup(recid){$('#rec'+recid).attr('data-animationappear','off');$('#rec'+recid).css('opacity','1');var el=$('#rec'+recid).find('.t-popup'),hook=el.attr('data-tooltip-hook'),analitics=el.attr('data-track-popup');if(hook!==''){$('.r').on('click','a[href="'+hook+'"]',function(e){t702_showPopup(recid);t702_resizePopup(recid);e.preventDefault();if(window.lazy=='y'){t_lazyload_update()}
if(analitics>''){var virtTitle=hook;if(virtTitle.substring(0,7)=='#popup:'){virtTitle=virtTitle.substring(7)}
Tilda.sendEventToStatistics(analitics,virtTitle)}})}}
function t702_onSuccess(t702_form){var t702_inputsWrapper=t702_form.find('.t-form__inputsbox');var t702_inputsHeight=t702_inputsWrapper.height();var t702_inputsOffset=t702_inputsWrapper.offset().top;var t702_inputsBottom=t702_inputsHeight+t702_inputsOffset;var t702_targetOffset=t702_form.find('.t-form__successbox').offset().top;if($(window).width()>960){var t702_target=t702_targetOffset-200}else{var t702_target=t702_targetOffset-100}
if(t702_targetOffset>$(window).scrollTop()||($(document).height()-t702_inputsBottom)<($(window).height()-100)){t702_inputsWrapper.addClass('t702__inputsbox_hidden');setTimeout(function(){if($(window).height()>$('.t-body').height()){$('.t-tildalabel').animate({opacity:0},50)}},300)}else{$('html, body').animate({scrollTop:t702_target},400);setTimeout(function(){t702_inputsWrapper.addClass('t702__inputsbox_hidden')},400)}
var successurl=t702_form.data('success-url');if(successurl&&successurl.length>0){setTimeout(function(){window.location.href=successurl},500)}}
function t702_lockScroll(){var body=$("body");if(!body.hasClass('t-body_scroll-locked')){var bodyScrollTop=(typeof window.pageYOffset!=='undefined')?window.pageYOffset:(document.documentElement||document.body.parentNode||document.body).scrollTop;body.addClass('t-body_scroll-locked');body.css("top","-"+bodyScrollTop+"px");body.attr("data-popup-scrolltop",bodyScrollTop)}}
function t702_unlockScroll(){var body=$("body");if(body.hasClass('t-body_scroll-locked')){var bodyScrollTop=$("body").attr("data-popup-scrolltop");body.removeClass('t-body_scroll-locked');body.css("top","");body.removeAttr("data-popup-scrolltop")
window.scrollTo(0,bodyScrollTop)}}
function t702_showPopup(recid){var el=$('#rec'+recid),popup=el.find('.t-popup');popup.css('display','block');el.find('.t-range').trigger('popupOpened');if(window.lazy=='y'){t_lazyload_update()}
setTimeout(function(){popup.find('.t-popup__container').addClass('t-popup__container-animated');popup.addClass('t-popup_show')},50);$('body').addClass('t-body_popupshowed t702__body_popupshowed');if(/iPhone|iPad|iPod/i.test(navigator.userAgent)&&!window.MSStream){setTimeout(function(){t702_lockScroll()},500)}
el.find('.t-popup').mousedown(function(e){var windowWidth=$(window).width();var maxScrollBarWidth=17;var windowWithoutScrollBar=windowWidth-maxScrollBarWidth;if(e.clientX>windowWithoutScrollBar){return}
if(e.target==this){t702_closePopup(recid)}});el.find('.t-popup__close').click(function(e){t702_closePopup(recid)});el.find('a[href*="#"]').click(function(e){var url=$(this).attr('href');if(!url||url.substring(0,7)!='#price:'){t702_closePopup(recid);if(!url||url.substring(0,7)=='#popup:'){setTimeout(function(){$('body').addClass('t-body_popupshowed')},300)}}});$(document).keydown(function(e){if(e.keyCode==27){t702_closePopup(recid)}})}
function t702_closePopup(recid){$('body').removeClass('t-body_popupshowed t702__body_popupshowed');$('#rec'+recid+' .t-popup').removeClass('t-popup_show');if(/iPhone|iPad|iPod/i.test(navigator.userAgent)&&!window.MSStream){t702_unlockScroll()}
setTimeout(function(){$('.t-popup').not('.t-popup_show').css('display','none')},300)}
function t702_resizePopup(recid){var el=$("#rec"+recid),div=el.find(".t-popup__container").height(),win=$(window).height()-120,popup=el.find(".t-popup__container");if(div>win){popup.addClass('t-popup__container-static')}else{popup.removeClass('t-popup__container-static')}}
function t702_sendPopupEventToStatistics(popupname){var virtPage='tilda/popup/index.html';var virtTitle='Popup: ';if(popupname.substring(0,7)=='#popup:'){popupname=popupname.substring(7)}
virtPage+=popupname;virtTitle+=popupname;if(window.Tilda&&typeof Tilda.sendEventToStatistics=='function'){Tilda.sendEventToStatistics(virtPage,virtTitle,'',0)}else{if(ga){if(window.mainTracker!='tilda'){ga('send',{'hitType':'pageview','page':virtPage,'title':virtTitle})}}
if(window.mainMetrika>''&&window[window.mainMetrika]){window[window.mainMetrika].hit(virtPage,{title:virtTitle,referer:window.location.href})}}}
$btnpaysubmit=!1;$(document).ready(function(){window.tildaGetPaymentForm=function(price,product,paysystem,blockid){var $allrecords=$('#allrecords');var formnexturl='htt'+'ps://forms.tildacdn'+'.com/payment/next/';var virtPage='/tilda/'+blockid+'/payment/';var virtTitle='Go to payment from '+blockid;if(window.Tilda&&typeof Tilda.sendEventToStatistics=='function'){Tilda.sendEventToStatistics(virtPage,virtTitle,product,price)}
$.ajax({type:"POST",url:formnexturl,data:{projectid:$allrecords.data('tilda-project-id'),formskey:$allrecords.data('tilda-formskey'),price:price,product:product,system:paysystem,recid:blockid},dataType:"json",success:function(json){$btnpaysubmit.removeClass('t-btn_sending');tildaBtnPaySubmit='0';if(json&&json.next&&json.next.type>''){var res=window.tildaForm.payment($('#'+blockid),json.next);successurl='';return!1}},fail:function(error){var txt;$btnpaysubmit.removeClass('t-btn_sending');tildaBtnPaySubmit='0';if(error&&error.responseText>''){txt=error.responseText+'. Please, try again later.'}else{if(error&&error.statusText){txt='Error ['+error.statusText+']. Please, try again later.'}else{txt='Unknown error. Please, try again later.'}}
alert(txt)},timeout:10*1000})};if(typeof tcart__cleanPrice=='undefined'){function tcart__cleanPrice(price){if(typeof price=='undefined'||price==''||price==0){price=0}else{price=price.replace(',','.');price=price.replace(/[^0-9\.]/g,'');price=parseFloat(price).toFixed(2);if(isNaN(price)){price=0}
price=parseFloat(price);price=price*1;if(price<0){price=0}}
return price}}
if(typeof tcart__escapeHtml=='undefined'){function tcart__escapeHtml(text){var map={'<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};return text.replace(/[<>"']/g,function(m){return map[m]})}}
if($('.js-payment-systembox').length>0){var tildaBtnPaySubmit='0';$('a[href^="#order"]').off('dblclick');$('a[href^="#order"]').off('click');$('a[href^="#order"]').click(function(e){e.preventDefault();if(tildaBtnPaySubmit=='1'){return!1}
if($('.t706').length>0){console.log('Conflict error: there are two incompatible blocks on the page: ST100 and ST105. Please go to Tilda Editor and delete one of these blocks.');return!1}
$btnpaysubmit=$(this);$btnpaysubmit.addClass('t-btn_sending');tildaBtnPaySubmit='1'
var tmp=$(this).attr('href');var arParam,price=0,product='';if(tmp.substring(0,7)=='#order:'){tmp=tmp.split(':');arParam=tmp[1].split('=');price=tcart__cleanPrice(arParam[1]);product=tcart__escapeHtml(arParam[0])}else{var pel=$(this).closest('.js-product');if(typeof pel!='undefined'){if(product==''){product=pel.find('.js-product-name').text();if(typeof product=='undefined'){product=''}}
if(price==''||price==0){price=pel.find('.js-product-price').text();price=tcart__cleanPrice(price)}
var optprice=0;var options=[];pel.find('.js-product-option').each(function(){var el_opt=$(this);var op_option=el_opt.find('.js-product-option-name').text();var op_variant=el_opt.find('option:selected').val();var op_price=el_opt.find('option:selected').attr('data-product-variant-price');op_price=tcart__cleanPrice(op_price);if(typeof op_option!='undefined'&&typeof op_variant!='undefined'){var obj={};if(op_option!=''){op_option=tcart__escapeHtml(op_option)}
if(op_variant!=''){op_variant=tcart__escapeHtml(op_variant);op_variant=op_variant.replace(/(?:\r\n|\r|\n)/g,'')}
if(op_option.length>1&&op_option.charAt(op_option.length-1)==':'){op_option=op_option.substring(0,op_option.length-1)}
optprice=optprice+parseFloat(op_price);options.push(op_option+'='+op_variant)}});if(options.length>0){product=product+': '+options.join(', ')}}}
var $parent=$(this).parent();var blockid=$(this).closest('.r').attr('id');var $paysystems=$('.js-dropdown-paysystem .js-payment-system');if(!product){var tmp=$(this).closest('.r').find('.title');if(tmp.length>0){product=tmp.text()}else{product=$(this).text()}}
if($paysystems.length==0){alert('Error: payment system is not assigned. Add payment system in the Site Settings.');$btnpaysubmit.removeClass('t-btn_sending');tildaBtnPaySubmit='0';return!1}
if($paysystems.length==1){tildaGetPaymentForm(price,product,$paysystems.data('payment-system'),blockid)}else{var $jspaybox=$('.js-payment-systembox');if($jspaybox.length>0){var $linkelem=$(this);var offset=$linkelem.offset();$jspaybox.css('top',offset.top+'px');$jspaybox.css('left',offset.left+'px');$jspaybox.css('margin-top','-45px');$jspaybox.css('margin-left','-25px');$jspaybox.css('position','absolute');$jspaybox.css('z-index','9999999');$jspaybox.appendTo($('body'));$(window).resize(function(){if($jspaybox.css('display')=='block'&&$linkelem){offset=$linkelem.offset();$jspaybox.css('top',offset.top+'px');$jspaybox.css('left',offset.left+'px')}});$jspaybox.show();function hideList(){$btnpaysubmit.removeClass('t-btn_sending');tildaBtnPaySubmit='0';$jspaybox.hide();$('.r').off('click',hideList);return!1}
$('.r').click(hideList);$('.js-payment-systembox a').off('dblclick');$('.js-payment-systembox a').off('click');$('.js-payment-systembox a').click(function(e){e.preventDefault();$jspaybox.hide();$linkelem=!1;tildaGetPaymentForm(price,product,$(this).data('payment-system'),blockid);return!1})}}
return!1})}});function t868_setHeight(recid){var rec=$('#rec'+recid);var div=rec.find('.t868__video-carier');var height=div.width()*0.5625;div.height(height);div.parent().height(height)}
function t868_initPopup(recid){var rec=$('#rec'+recid);$('#rec'+recid).attr('data-animationappear','off');$('#rec'+recid).css('opacity','1');var el=$('#rec'+recid).find('.t-popup');var hook=el.attr('data-tooltip-hook');var analitics=el.attr('data-track-popup');var customCodeHTML=t868__readCustomCode(rec);if(hook!==''){$('.r').on('click','a[href="'+hook+'"]',function(e){t868_showPopup(recid,customCodeHTML);t868_resizePopup(recid);e.preventDefault();if(analitics>''){var virtTitle=hook;if(virtTitle.substring(0,7)=='#popup:'){virtTitle=virtTitle.substring(7)}
Tilda.sendEventToStatistics(analitics,virtTitle)}})}}
function t868__readCustomCode(rec){var customCode=rec.find('.t868 .t868__code-wrap').html();rec.find('.t868 .t868__code-wrap').remove();return customCode}
function t868_showPopup(recid,customCodeHTML){var rec=$('#rec'+recid);var popup=rec.find('.t-popup');var popupContainer=rec.find('.t-popup__container');popupContainer.append(customCodeHTML);popup.css('display','block');t868_setHeight(recid);setTimeout(function(){popup.find('.t-popup__container').addClass('t-popup__container-animated');popup.addClass('t-popup_show')},50);$('body').addClass('t-body_popupshowed');rec.find('.t-popup').mousedown(function(e){if(e.target==this){t868_closePopup(recid)}});rec.find('.t-popup__close').click(function(e){t868_closePopup(recid)});rec.find('a[href*=#]').click(function(e){var url=$(this).attr('href');if(url.indexOf('#order')!=-1){var popupContainer=rec.find('.t-popup__container');setTimeout(function(){popupContainer.empty()},600)}
if(!url||url.substring(0,7)!='#price:'){t868_closePopup();if(!url||url.substring(0,7)=='#popup:'){setTimeout(function(){$('body').addClass('t-body_popupshowed')},300)}}});$(document).keydown(function(e){if(e.keyCode==27){t868_closePopup(recid)}})}
function t868_closePopup(recid){var rec=$('#rec'+recid);var popup=rec.find('.t-popup');var popupContainer=rec.find('.t-popup__container');$('body').removeClass('t-body_popupshowed');$('#rec'+recid+' .t-popup').removeClass('t-popup_show');popupContainer.empty();setTimeout(function(){$('.t-popup').not('.t-popup_show').css('display','none')},300)}
function t868_resizePopup(recid){var rec=$('#rec'+recid);var div=rec.find('.t-popup__container').height();var win=$(window).height();var popup=rec.find('.t-popup__container');if(div>win){popup.addClass('t-popup__container-static')}else{popup.removeClass('t-popup__container-static')}}
function t868_sendPopupEventToStatistics(popupname){var virtPage='/tilda/popup/';var virtTitle='Popup: ';if(popupname.substring(0,7)=='#popup:'){popupname=popupname.substring(7)}
virtPage+=popupname;virtTitle+=popupname;if(ga){if(window.mainTracker!='tilda'){ga('send',{'hitType':'pageview','page':virtPage,'title':virtTitle})}}
if(window.mainMetrika>''&&window[window.mainMetrika]){window[window.mainMetrika].hit(virtPage,{title:virtTitle,referer:window.location.href})}}
function t922_init(recid){setTimeout(function(){$('#rec'+recid+' .t-cover__carrier').addClass('js-product-img');t_prod__init(recid)},500)}