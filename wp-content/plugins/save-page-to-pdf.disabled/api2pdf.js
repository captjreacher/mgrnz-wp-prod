window.SavePageToPdf = function (){
document.body.style.cursor='wait';
jQuery.ajax({
    type: "post",url: window.Api2PdfWpAjaxUrl,data: { action: 'api2pdf_ajax', url: window.location.href },		
    success: function(data){ //so, if data is retrieved, store it in html
        window.location.href = JSON.parse(data).pdf;
        document.body.style.cursor='default';
    }
});};