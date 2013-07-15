$(document).on("ready", function(){
	// init fixed menu
	$(window).scroll(function(){
				if($("#bottom-nav").css("position") == "fixed" && ($('#registration').offset().top <= $('#bottom-nav').offset().top))
					$("#bottom-nav").css("position", "static");
	
				if($("#bottom-nav").css("position") == "static" && ($(document).scrollTop() + $(window).height()) < $('#registration').offset().top)
						$("#bottom-nav").css("position", "fixed");
		})

	// Init menus
	var v;
	$('#menu-root').collapsible({
		effect: 'none',             // The effect to use when expanding and collapsiing the menu. Accepts none, slide, or fade. Default: none.
		initialCollapse: true       // When true, collapses the menu when the page loads. Default: true
	});
	
	$('#main-selecttion').collapsible({
		effect: 'slide',             // The effect to use when expanding and collapsiing the menu. Accepts none, slide, or fade. Default: none.
		initialCollapse: false       // When true, collapses the menu when the page loads. Default: true
	});
	
	$('#countries_info').collapsible({
		effect: 'true',             // The effect to use when expanding and collapsiing the menu. Accepts none, slide, or fade. Default: none.
		initialCollapse: true   
	})
	
	var _countries = ['BY','CZ','FI','FR','DE','GR','IL','LT','NL','RU','SK','GB','UA'];
	var countries = [];
	var countriesMenuItemsByCode = {}
	var currentEl = 0;
	
	var countries_menu_items = $("#countries_info > li")
	for(var i = 0; i < _countries.length; i++)
	{
		countries[_countries[i]] = 1
		countriesMenuItemsByCode[_countries[i]] = countries_menu_items[i*2]
		var closeHandler = 
		$(countriesMenuItemsByCode[_countries[i]]).on("click", function(ev){
				console.log("currentEl: " + currentEl + " ev.toElement: " + ev)
				if(ev)
				{
					if(currentEl)
						$($(currentEl).siblings[0]).hide()
					currentEl = ev.toElement
				}
			})
		// console.log(i*2 + ": " + countries_menu_items[i*2].innerHTML)
	}
	
	console.log(countries)
	vectorMap = new jvm.WorldMap({
						map: 'world_mill_en',
						container: $('#map'),
						backgroundColor: 'none',
						regionStyle: {
							initial: {
								fill: '#FF8E32'
							},
							selected: {
								fill: '#F4A582'
							}
						},
						series: {
							regions: [{
								scale: {'1':'white', undefined:'red'},
								attribute: 'fill'
							}]
						},
						onRegionClick: function(e, code, el){
							//el.html(el.html()+' - '+code);
							if(currentEl)
								$(currentEl).click()

							$(countriesMenuItemsByCode[code]).trigger("click")

							console.log(code)
							console.log(countriesMenuItemsByCode)
							console.log(countriesMenuItemsByCode[code])
							
						}
					});
	
	vectorMap.series.regions[0].setValues(countries);
	vectorMap.setFocus(_countries)
	vectorMap.setFocus(4.5,.55,.36)
	console.log(vectorMap)
})