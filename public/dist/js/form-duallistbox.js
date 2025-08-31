/*=========================================================================================
    File Name: form-duallistbox.js
    Description: Dual list box js
    ----------------------------------------------------------------------------------------
    Item Name: Robust - Responsive Admin Template
    Version: 2.1
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function(window, document, $) {
	'use strict';

	// Basic Dual Listbox
	$('.duallistbox').bootstrapDualListbox();

	// Without Filter
	$('.duallistbox-no-filter').bootstrapDualListbox({
		showFilterInputs: false
	});

	// Multi selection Dual Listbox
	$('.duallistbox-multi-selection').bootstrapDualListbox({
    nonSelectedListLabel: 'Daftar Kontak',
    selectedListLabel: 'Selected',
    preserveSelectionOnMove: 'moved',
    moveOnSelect: false
  });

	//With Filter Options
  $('.duallistbox-with-filter').bootstrapDualListbox({
    nonSelectedListLabel: 'Daftar Kontak',
    selectedListLabel: 'Daftar Kontak dalam Grup',
    moveOnSelect: false,
  });
})(window, document, jQuery);