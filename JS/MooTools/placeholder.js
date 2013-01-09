Element.implement({
    clearFocusResetBlur: function(attr){
        var valueString = this.get(attr);
        this.addEvents({
            'focus': function(){
                if( this.get('value') == valueString ) {
                    this.set('value','');
                }
            },
            'blur': function(){
                if( this.get('value') == "" ) {
                    this.set('value',valueString);
                }
            }
        });
    }
});

/**
Usage:

HTML:
<input id="input_id" value="some value" placeholder="default value" />

JS:
if (!Modernizr.input.placeholder) {
    $('input_id').clearFocusResetBlur('placeholder');
}
**/