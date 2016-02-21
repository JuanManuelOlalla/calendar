function alerts() {
    "use strict";

    var elem, hideHandler, that = {};

    that.init = function(options) {
        elem = $(options.selector);
    };

    that.show = function(text, tipo) {
        clearTimeout(hideHandler);

        elem.find("span").html(text);
        elem.addClass(tipo);
        elem.delay(100).fadeIn().delay(2600).fadeOut();
    };
    
    that.ocultar = function(){
        if(elem){
            elem.hide();
        }
    }

    return that;
};

var Example0 = alerts();
var Example1 = alerts();
var Example2 = alerts();
var Example3 = alerts();
var Example4 = alerts();

function ocultarAlerts(){
    Example0.ocultar();
    Example1.ocultar();
    Example2.ocultar();
    Example3.ocultar();
    Example4.ocultar();
}
