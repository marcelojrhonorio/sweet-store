$(function() {
	var maskPhoneDDDBehavior = function (val) {
		  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
		},
		optionsPhoneDDD = {onKeyPress: function(val, e, field, options) {
		        field.mask(maskPhoneDDDBehavior.apply({}, arguments), options);
		    }
		};
	$('.mask-phone-ddd').mask(maskPhoneDDDBehavior, optionsPhoneDDD);

	var maskPhoneBehavior = function (val) {
		  return val.replace(/\D/g, '').length >= 9 ? '00000-0000' : '0000-00009';
		},
		optionsPhone = {onKeyPress: function(val, e, field, options) {
		        field.mask(maskPhoneBehavior.apply({}, arguments), options);
		    }
		};
	$('.mask-phone').mask(maskPhoneBehavior, optionsPhone);
	$('.mask-phone-home').mask("(00) 0000-0000");
	$('.mask-phone-cell').mask("(00) 00000-0000");
	$('.mask-ddd').mask("00");
	$('.mask-cnpj').mask("00.000.000/0000-00");
	$('.mask-cpf').mask("000.000.000-00");
});

