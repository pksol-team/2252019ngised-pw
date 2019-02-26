<?php
/**
*
*	(p) package: Lumise
*	(c) author:	King-Theme
*	(i) website: https://lumise.com
*
*/

class lumise_tmpl_register {
	
	public function reg_editor_menus() {

		global $lumise;
		
		return array(
			
			'product' => array(
				"label" => $lumise->lang('Product'),
				"icon" => "lumisex-cube",
				"callback" => "",
				"load" => "",
				"content" =>
					'<header>
						<name></name>
						<price></price>
						<sku></sku>
						<button class="lumise-btn white" id="lumise-change-product">
							'.$lumise->lang('Change product').'
							<i class="lumisex-arrow-swap"></i>
						</button>
						<desc>
							<span></span>
							&nbsp;&nbsp;<a href="#more">'.$lumise->lang('More').'</a>
						</desc>
					</header>
					<div id="lumise-cart-wrp" data-view="attributes" class="smooth">
						<div class="lumise-cart-options">
							<div class="lumise-prints"></div>
							<div class="lumise-cart-attributes" id="lumise-cart-attributes"></div>
						</div>
					</div>'
			),
			
			'templates' => array(
				"label" => $lumise->lang('Templates'),
				"icon" => "lumise-icon-star",
				"callback" => "",
				"load" => "templates",
				"class" => "lumise-x-thumbn",
				"content" =>
					'<header>
						<span class="lumise-templates-search">
							<input type="search" id="lumise-templates-search-inp" placeholder="'.$lumise->lang('Search templates').'" />
							<i class="lumisex-android-search"></i>
						</span>
						<div class="lumise-template-categories" data-prevent-click="true">
							<button data-func="show-categories" data-type="templates">
								<span>'.$lumise->lang('All categories').'</span>
								<i class="lumisex-ios-arrow-forward"></i>
							</button>
						</div>
					</header>
					<div id="lumise-templates-list" class="smooth">
						<ul class="lumise-list-items">
							<i class="lumise-spinner white x3 mt2"></i>
						</ul>
					</div>'
			),
			
			'cliparts' => array(
				"label" => $lumise->lang('Cliparts'),
				"icon" => "lumise-icon-heart",
				"callback" => "",
				"load" => "cliparts",
				"class" => "lumise-x-thumbn",
				"content" =>
					'<header>
						<span class="lumise-cliparts-search">
							<input type="search" id="lumise-cliparts-search-inp" placeholder="'.$lumise->lang('Search cliparts').'" />
							<i class="lumisex-android-search"></i>
						</span>
						<div class="lumise-clipart-categories" data-prevent-click="true">
							<button data-func="show-categories" data-type="cliparts">
								<span>'.$lumise->lang('All categories').'</span>
								<i class="lumisex-ios-arrow-forward"></i>
							</button>
						</div>
					</header>
					<div id="lumise-cliparts-list" class="smooth">
						<ul class="lumise-list-items">
							<i class="lumise-spinner white x3 mt2"></i>
						</ul>
					</div>'
			),
			
			'text' => array(
				"label" => $lumise->lang('Text'),
				"icon" => "lumisex-character",
				"callback" => "",
				"load" => "",
				"class" => "smooth",
				"content" =>
					'<p class="gray">'.$lumise->lang('Click or drag to add text').'</p>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "CurvedText", "fontSize": 30, "font":["","regular"],"bridge":{"bottom":2,"curve":-4.5,"oblique":false,"offsetY":0.5,"trident":false},"type":"curvedText"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-curved.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "10", "fontSize": 100, "font":["","regular"],"type":"i-text", "charSpacing": 40, "top": -50},{"fontFamily":"Poppins","text": "Messi", "fontSize": 30, "font":["","regular"],"type":"i-text", "charSpacing": 40, "top": 10}]\' style="text-align: center;">
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-number.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Oblique","fontSize":60,"font":["","regular"],"bridge":{"bottom":4.5,"curve":10,"oblique":true,"offsetY":0.5,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-oblique.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2,"curve":-4.5,"oblique":false,"offsetY":0.5,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-1.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2,"curve":-2.5,"oblique":false,"offsetY":0.1,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-2.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2,"curve":-3,"oblique":false,"offsetY":0.5,"trident":true},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-3.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":5,"curve":5,"oblique":false,"offsetY":0.5,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-4.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":2.5,"curve":2.5,"oblique":false,"offsetY":0.05,"trident":false},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-5.png" />
					</span>
					<span draggable="true" data-act="add" data-ops=\'[{"fontFamily":"Anton","text": "Bridge","fontSize":70,"font":["","regular"],"bridge":{"bottom":3,"curve":2.5,"oblique":false,"offsetY":0.5,"trident":true},"type":"text-fx"}]\'>
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-bridge-6.png" />
					</span>
					<span id="lumise-text-mask-guide">
						<img height="70" src="'.$lumise->cfg->assets_url.'assets/images/text-sample-mask.png" />
					</span>
					<div id="lumise-text-ext"></div>'.
					($lumise->connector->is_admin() || $lumise->cfg->settings['user_font'] !== '0' ? '<button class="lumise-btn mb2 lumise-more-fonts">'.$lumise->lang('Load more 878+ fonts').'</button>' : '')
			),
			
			'uploads' => array(
				"label" => $lumise->lang('Images'),
				"icon" => "lumise-icon-picture",
				"callback" => "",
				"load" => "images",
				"class" => "lumise-x-thumbn",
				"content" =>
					(($lumise->connector->is_admin() || $lumise->cfg->settings['disable_resources'] != 1) ? 
					'<header class="images-from-socials lumise_form_group">
						<button class="active" data-nav="internal">
							<i class="lumise-icon-cloud-upload"></i>
							'.$lumise->lang('Upload').'
						</button>
						<button data-nav="external">
							<i class="lumise-icon-magnifier"></i>
							'.$lumise->lang('Resources').'
						</button>
					</header>' : '').
					'<div data-tab="internal" class="active">
						<div id="lumise-upload-form">
							<i class="lumise-icon-cloud-upload"></i>
							<span>'.$lumise->lang('Click or drop images here').'</span>
							<input type="file" multiple="true" />
						</div>
						<div id="lumise-upload-list">
							<ul class="lumise-list-items"></ul>
						</div>
					</div>
					<div data-tab="external" id="lumise-external-images"></div>'
			),

			'design_text' => array(
				"label" => $lumise->lang('Design'),
				"icon" => "lumisex-paintbucket",
				"callback" => "",
				"load" => "",
				"class" => "smooth",
				"content" => '


				<div class="test">
					<div class="select-colors">
						<p>2.Select Colors:</p>
						<hr>
						<div class="gradient-img">
							<div data-type="gradient0" data-no-of-colors="1" class="gradient img-setting">&nbsp;</div>
							<div data-type="gradient1" data-no-of-colors="2" class="gradient1 img-setting">&nbsp;</div>
							<div data-type="gradient2" data-no-of-colors="2" class="gradient2 img-setting">&nbsp;</div>
							<div data-type="gradient3" data-no-of-colors="2" class="gradient3 img-setting">&nbsp;</div>
							<div data-type="gradient4" data-no-of-colors="2" class="gradient4 img-setting">&nbsp;</div>
							<div data-type="gradient5" data-no-of-colors="1" class="gradient5 img-setting">&nbsp;</div>
							<div data-type="gradient6" data-no-of-colors="1" class="gradient6 img-setting">&nbsp;</div>
							<div data-type="gradient7" data-no-of-colors="2" class="gradient7 img-setting">&nbsp;</div>
							<div data-type="gradient8" data-no-of-colors="2" class="gradient8 img-setting">&nbsp;</div>
							<div data-type="gradient9" data-no-of-colors="4" class="gradient9 img-setting">&nbsp;</div>
						</div>
						<div class="color-buttons">
							<button type="button">Select First Color</button>
							<button type="button">Select Second Color</button>
							<button type="button">Select Third Color</button>
							<button type="button">Select Forth Color</button>
						</div>
						<div class="colors disabled-colors">
							<div data-color="#000000" class="color-main color-black">&nbsp;</div>
							<div data-color="#262626" class="color-main color-black1">&nbsp;</div>
							<div data-color="#444444" class="color-main color-black2">&nbsp;</div>
							<div data-color="#7b7b7b" class="color-main color-grey">&nbsp;</div>
							<div data-color="#b6b6b7" class="color-main color-grey1">&nbsp;</div>
							<div data-color="#ffffff" class="color-main color-white">&nbsp;</div>
							<div data-color="#c7001b" class="color-main color-red">&nbsp;</div>
							<div data-color="#ff5900" class="color-main color-orange">&nbsp;</div>
							<div data-color="#ff8800" class="color-main color-sun">&nbsp;</div>
							<div data-color="#ffb700" class="color-main color-yellow">&nbsp;</div>
							<div data-color="#fdd100" class="color-main color-yellow1">&nbsp;</div>
							<div data-color="#ffff00" class="color-main color-yellow2">&nbsp;</div>
							<div data-color="#a1050d" class="color-main color-mahroon">&nbsp;</div>
							<div data-color="#5e3301" class="color-main color-brown">&nbsp;</div>
							<div data-color="#a36c41" class="color-main color-brown1">&nbsp;</div>
							<div data-color="#ba6510" class="color-main color-peach">&nbsp;</div>
							<div data-color="#cf9b00" class="color-main color-dimyellow">&nbsp;</div>
							<div data-color="#e6d7be" class="color-main color-offwhite">&nbsp;</div>
							<div data-color="#740107" class="color-main color-darkred">&nbsp;</div>
							<div data-color="#6b00b7" class="color-main color-darkpurple">&nbsp;</div>
							<div data-color="#4500a9" class="color-main color-purple">&nbsp;</div>
							<div data-color="#e128a0" class="color-main color-pink">&nbsp;</div>
							<div data-color="#ff75e6" class="color-main color-lightpink">&nbsp;</div>
							<div data-color="#d296ff" class="color-main color-pink1">&nbsp;</div>
							<div data-color="#022d84" class="color-main color-blue">&nbsp;</div>
							<div data-color="#2b18d8" class="color-main color-darkblue">&nbsp;</div>
							<div data-color="#005bb1" class="color-main color-sky">&nbsp;</div>
							<div data-color="#0087db" class="color-main color-skyblue">&nbsp;</div>
							<div data-color="#4dc1ff" class="color-main color-skyblue1">&nbsp;</div>
							<div data-color="#824cff" class="color-main color-lightpurple">&nbsp;</div>
							<div data-color="#002c54" class="color-main color-navyblue">&nbsp;</div>
							<div data-color="#097871" class="color-main color-navyblue1">&nbsp;</div>
							<div data-color="#57d100" class="color-main color-lightgreen">&nbsp;</div>
							<div data-color="#238a20" class="color-main color-green">&nbsp;</div>
							<div data-color="#046025" class="color-main color-darkgreen">&nbsp;</div>
							<div data-color="#0f3f0e" class="color-main color-darkgreen1">&nbsp;</div>
						</div>
					</div>
					<div class="shadow-outline">
						<p>3.Shadow/Outline:</p>
						<hr>
						<div class="shadow-btn">
							<button class="outline">Outline</button>
							<button class="shadow">Shadow</button>
						</div>
					</div>
					<div class="otline-selection-box">
						<div class="otline-selection-box-num">
							
							<div class="label-numb">
								<div class="label-outline">
									<p>Outline Thickness</p>
								</div>
								<div class="input-outline">
									<div class="input-centre">
										<input type="button" value="-" class="minus input-group-addon">
										<input id="thickness" type="text" min="0" max="100" class="value Computer-input" value="0" disabled="">
										<input type="button" value="+" class="plus input-group-addon">
									</div>
								</div>
							</div>

							<div class="label-numb">
								<div class="label-outline">
									<p>Horizontal Offset</p>
								</div>
								<div class="input-outline">
									<div class="input-centre">
										<input type="button" value="-" class="minus input-group-addon">
										<input id="x-offset" type="text" min="0" max="100" class="value Computer-input" value="0" readonly="">
										<input type="button" value="+" class="plus input-group-addon">
									</div>
								</div>
							</div>
							<div class="label-numb">
								<div class="label-outline">
									<p>vertical Offset</p>
								</div>
								<div class="input-outline">
									<div class="input-centre">
										<input type="button" value="-" class="minus input-group-addon">
										<input id="y-offset" type="text" min="0" max="100" class="value Computer-input" value="0" disabled="">
										<input type="button" value="+" class="plus input-group-addon">
									</div>
								</div>
							</div>
						</div>
						

						<!-- <div class="otline-selection-img">
							<div class="qr-imgs">
								<ul>
									<li class="qr-img1">&nbsp;</li>
									<li class="qr-img2">&nbsp;</li>
									<li class="qr-img3">&nbsp;</li>
									<li class="qr-img4">&nbsp;</li>
								</ul>
							</div>
						</div> -->


					</div>
					<div class="screen-fill">
						<p>3.Screen Fill:</p>
						<hr>
						<div class="archimedes">
							<label>Screen Fill: <span> Archimedes </span></label>
						</div>
						<div class="screen-img">
							&nbsp;
						</div>
						<div class="check_boxes">
							<ul>
								<li>
									<input type="checkbox" name="vehicle1" value="Invert">
									<span class="invert">invert</span>
								</li>
								<li>
									<input type="checkbox" name="vehicle1" value="Invert">
									<span class="radius">Radius</span>
								</li>
							</ul>
						</div>
						<div class="op-density">
							<div class="label-numb">
								<div class="label-outline-op">
									<p>Opacity
									</p>
								</div>
								<div class="input-outline">
									<div class="input-centre">
										<input type="button" value="-" class="minus input-group-addon">
										<input id="Opacity" type="text" min="0" max="100" class="value Computer-input" value="0" disabled="">
										<input type="button" value="+" class="plus input-group-addon">
									</div>
								</div>
							</div>
							<div class="label-numb">
								<div class="label-outline-op">
									<p>Density
									</p>
								</div>
								<div class="input-outline">
									<div class="input-centre">
										<input type="button" value="-" class="minus input-group-addon">
										<input id="Density" type="text" min="0" max="100" class="value Computer-input" value="0" disabled="">
										<input type="button" value="+" class="plus input-group-addon">
									</div>
								</div>
							</div>
						</div>
					</div>
					<button type="button" class="apply-designs-tab">Apply</button>
				</div>





				'
			),
			
			'shapes' => array(
				"label" => $lumise->lang('Shapes'),
				"icon" => "lumisex-diamond",
				"callback" => "",
				"load" => "shapes",
				"class" => "smooth",
				"content" => ""
			),
			
			'layers' => array(
				"label" => $lumise->lang('Layers'),
				"icon" => "lumise-icon-layers",
				"callback" => "layers",
				"load" => "",
				"class" => "smooth",
				"content" => "<ul></ul>"
			),
			
			'drawing' => array(
				"label" => $lumise->lang('Drawing'),
				"icon" => "lumise-icon-note",
				"callback" => "",
				"load" => "",
				"class" => "lumise-left-form",
				"content" => 
					'<h3>'.$lumise->lang('Free drawing mode').'</h3>
					<div>
						<label>'.$lumise->lang('Size').'</label>
						<inp data-range="helper" data-value="1">
							<input id="lumise-drawing-width" data-callback="drawing" value="1" min="1" max="100" data-value="1" type="range" />
						</inp>
					</div>
					<div'.($lumise->cfg->settings['enable_colors'] == '0' ? ' class="hidden"' : '').'>
						<input id="lumise-drawing-color" placeholder="'.$lumise->lang('Click to choose color').'" type="search" class="color" />
						<span class="lumise-save-color" data-tip="true" data-target="drawing-color">
							<i class="lumisex-android-add"></i>
							<span>'.$lumise->lang('Save this color').'</span>
						</span>
					</div>
					<div>
						<ul class="lumise-color-presets" data-target="drawing-color"></ul>
					</div>
					<div class="gray">
						<span>
							<i class="lumisex-android-bulb"></i>
							'.$lumise->lang('Tips: Mouse wheel on the canvas to quick change the brush size').'
						</span>
					</div>'
			)
		);
	}
	
	public function reg_product_attributes() {
		
		global $lumise;
		
		return array(
			
			'printing' => array(
				'hidden' => true,
				'render' => ''
			),
			
			'select' => array(
				'label' => $lumise->lang('Drop down'),
				'use_variation' => true,
				'render' => <<<EOF
				
					var el = '<select name="'+data.id+'" class="lumise-cart-param" '+(data.required ? 'required' : '')+'>';
					data.values.map(function (op){
						el += '<option value="'+op.value.replace(/\"/g, '&quot;')+'"'+(data.value == op.value ? ' selected' : '')+'>'+op.title+'</option>';
					});
					
					el += '</select>';
					
					return $(el);				
EOF
			),
			
			'product_color' => array(
				'label' => $lumise->lang('Product colors'),
				'unique' => true,
				'use_variation' => true,
				'values' => <<<EOF
				
					var colors = values.split(decodeURI('%0A')),
						content = '<div class="lumise-field-color-wrp rbd">\
								<ul class="lumise-field-color">';
					if (values !== '') {
						colors.map(function(c) {
					
							var c = c.split('|'),
								lb = (c[1] !== undefined ? c[1].trim() : c[0].trim());
							
							lb = lb.replace(/\"/g, '&quot;');
							
							content += '<li data-color="'+c[0].trim()+'" data-label="'+lb+'" style="background:'+c[0].trim()+'"><i class="fa fa-times" data-func="delete"></i></li>';
						
						});
					}
					
					content += '</ul>';
					
					content += '<p style="padding-top: 0px;">\
									<button class="lumise-button lumise-button-primary" data-func="create-color">\
										<i class="fa fa-plus"></i> {$lumise->lang('Add new color')}\
									</button>\
									<button class="lumise-button" data-func="clear-color">\
										<i class="fa fa-eraser"></i> {$lumise->lang('Clear all')}\
									</button>\
									<textarea data-name="values" class="hidden">'+(values !== undefined ? values : '')+'</textarea>\
								</p>\
								<p><em>{$lumise->lang('This will change the color of the product, apply to products with mask image (PNG)')}</em></p>\
							</div>';
							
					wrp.html(content);
					
					if (typeof wrp.sortable == 'function') {
						wrp.find('ul.lumise-field-color').sortable({update: function() {
							var vals = [];
							$(this).find('li[data-color]').each(function() {
								vals.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));	
							});
							$(this).closest('.lumise-field-color-wrp').find('textarea[data-name="values"]').val(
								vals.join(decodeURI('%0A'))
							).trigger('change');
						}});
					};
					
					triggerObjects.general_events.return_colors = function(wrp) {
		
						var val = [];
						
						wrp.find('li[data-color]').each(function(){
							val.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));
						});
						
						val = val.join(decodeURI('%0A'));
						
						wrp.find('textarea[data-name="values"]').val(val).trigger('change');
							
					};
					
					trigger({
						el: wrp,
						events: {
							'button[data-func="create-color"]': 'add_color',
							'button[data-func="clear-color"]': 'clear_color',
							'ul.lumise-field-color': 'color_func'
						},
						add_color: function(e) {
							e.data = triggerObjects.general_events;
							triggerObjects.general_events.create_color(e);
						},
						clear_color: function(e) {
							$(this).closest('.att-layout-body-field').find('ul.lumise-field-color').html('');
							triggerObjects.general_events.return_colors(
								$(this).closest('.att-layout-body-field')
							);
							e.preventDefault();	
						},
						color_func: function(e) {
							if (
								e.target.getAttribute('data-func') == 'delete' ||
								e.target.getAttribute('data-color') == 'delete'
							) {
								$(e.target).parent().remove();
								triggerObjects.general_events.return_colors(
									$(this).closest('.att-layout-body-field')
								);
								e.preventDefault();	
							}	
						}
					});
					
EOF
				,'render' => <<<EOF
				
					var el = $('<ul class="lumise-product-color"></ul>'), 
						valid_value = false;
						
					el.append('<li data-color="" title="{$lumise->lang('Clear color')}"></li>');
					
					data.values.map(function(v) {
						if (v.value !== '') {
							el.append('<li data-color="'+v.value+'" title="'+v.title.replace(/\"/g, '&quot;')+'" style="background-color:'+v.value+'"></li>');
							if (data.value === v.value)
								valid_value = true;
						}
					});
					
					el.append('<input type="hidden" name="'+data.id+'" class="lumise-cart-param" value="'+(valid_value ? data.value : '')+'" '+(data.required ? 'required' : '')+' />');
					
					el.find('li[data-color]').on('click', function(e) {
						$(this).parent().find('li.choosed').removeClass('choosed');
						$(this).addClass('choosed')
							   .closest('.lumise_form_content')
							   .find('input.lumise-cart-param')
							   .val(this.getAttribute('data-color'))
							   .trigger('change');
						setTimeout(lumise.func.product_color, 1, this.getAttribute('data-color'));
						e.preventDefault();
					});
					
					if (valid_value && data.value !== undefined && data.value !== '')
						el.find('li[data-color="'+data.value+'"]').trigger('click');
					
					return el;
						
EOF
			),
			
			'color' => array(
				'label' => $lumise->lang('Color picker'),
				'use_variation' => true,
				'values' => <<<EOF
				
					var colors = values.split(decodeURI('%0A')),
						content = '<div class="lumise-field-color-wrp rbd">\
								<ul class="lumise-field-color">';
					if (values !== '') {
						colors.map(function(c) {
					
							var c = c.split('|'),
								lb = (c[1] !== undefined ? c[1].trim() : c[0].trim());
							
							lb = lb.replace(/\"/g, '&quot;');
							
							content += '<li data-color="'+c[0].trim()+'" data-label="'+lb+'" style="background:'+c[0].trim()+'"><i class="fa fa-times" data-func="delete"></i></li>';
						
						});
					}
					
					content += '</ul>';
					
					content += '<p style="padding-top: 0px;">\
									<button class="lumise-button lumise-button-primary" data-func="create-color">\
										<i class="fa fa-plus"></i> {$lumise->lang('Add new color')}\
									</button>\
									<button class="lumise-button" data-func="clear-color">\
										<i class="fa fa-eraser"></i> {$lumise->lang('Clear all')}\
									</button>\
									<textarea data-name="values" class="hidden">'+(values !== undefined ? values : '')+'</textarea>\
								</p>\
							</div>';
							
					wrp.html(content);
					
					if (typeof wrp.sortable == 'function') {
						wrp.find('ul.lumise-field-color').sortable({update: function() {
							var vals = [];
							$(this).find('li[data-color]').each(function() {
								vals.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));	
							});
							$(this).closest('.lumise-field-color-wrp').find('textarea[data-name="values"]').val(
								vals.join(decodeURI('%0A'))
							).trigger('change');
						}});
					};
					
					triggerObjects.general_events.return_colors = function(wrp) {
		
						var val = [];
						
						wrp.find('li[data-color]').each(function(){
							val.push(this.getAttribute('data-color')+'|'+this.getAttribute('data-label'));
						});
						
						val = val.join(decodeURI('%0A'));
						
						wrp.find('textarea[data-name="values"]').val(val).trigger('change');
							
					};
					
					trigger({
						el: wrp,
						events: {
							'button[data-func="create-color"]': 'add_color',
							'button[data-func="clear-color"]': 'clear_color',
							'ul.lumise-field-color': 'color_func'
						},
						add_color: function(e) {
							e.data = triggerObjects.general_events;
							triggerObjects.general_events.create_color(e);
						},
						clear_color: function(e) {
							$(this).closest('.att-layout-body-field').find('ul.lumise-field-color').html('');
							triggerObjects.general_events.return_colors(
								$(this).closest('.att-layout-body-field')
							);
							e.preventDefault();	
						},
						color_func: function(e) {
							if (
								e.target.getAttribute('data-func') == 'delete' ||
								e.target.getAttribute('data-color') == 'delete'
							) {
								$(e.target).parent().remove();
								triggerObjects.general_events.return_colors(
									$(this).closest('.att-layout-body-field')
								);
								e.preventDefault();	
							}	
						}
					});
					
EOF
				,'render' => <<<EOF
				
					var el = $('<ul class="lumise-product-color"></ul>'), valid_value = false;
					
					el.append('<li data-color="" title="{$lumise->lang('Clear color')}"></li>');
					
					data.values.map(function(v) {
						if (v.value !== '') {
							el.append('<li data-color="'+v.value+'" title="'+v.title.replace(/\"/g, '&quot;')+'" style="background-color:'+v.value+'"></li>');
							if (data.value === v.value)
								valid_value = true;
						}
					});
					
					el.append('<input type="hidden" name="'+data.id+'" class="lumise-cart-param" value="'+(valid_value ? data.value : '')+'" '+(data.required ? 'required' : '')+' />');
					
					el.find('li[data-color]').on('click', function(e) {
						$(this).parent().find('li.choosed').removeClass('choosed');
						$(this).addClass('choosed')
							   .closest('.lumise_form_content')
							   .find('input.lumise-cart-param')
							   .val(this.getAttribute('data-color'))
							   .trigger('change');
						e.preventDefault();
					});
					
					if (valid_value && data.value !== undefined && data.value !== '')
						el.find('li[data-color="'+data.value+'"]').trigger('click');
					
					return el;
						
EOF
			),
			
			'input' => array(
				'label' => $lumise->lang('Input text'),
				'default' => '',
				'placeholder' => '',
				'render' => <<<EOF
					return '<input type="text" name="'+data.id+'" class="lumise-cart-param" value="'+data.value+'" '+(data.required ? 'required' : '')+' />';			
EOF
			),
			
			'text' => array(
				'label' => $lumise->lang('Textarea'),
				'default' => '',
				'placeholder' => '',
				'render' => <<<EOF
					return '<textarea type="text" name="'+data.id+'" class="lumise-cart-param" '+(data.required ? 'required' : '')+'>'+data.value.replace(/\>/g, '&gt;').replace(/\</g, '&lt;')+'</textarea>';			
EOF
			),
			
			'checkbox' => array(
				'label' => $lumise->lang('Multiple checkbox'),
				'render' => <<<EOF
					
					var wrp = $('<div class="lumise_checkboxes"></div>');
					
					if (!data.value)
						data.value = [];
					else if (typeof data.value == 'string')
						data.value = data.value.split(decodeURI("%0A"));
					
					data.values.map(function(op) {
						
						var new_op 	= '<div class="lumise_checkbox">';
						
						new_op 	+= '<input type="checkbox" name="'+data.id+'" class="lumise-cart-param action_check" value="'+op.value+'" id="'+(data.id + '-' +op.value)+'" '+(data.required ? 'required' : '')+' '+(data.value.indexOf(op.value) > -1 ? 'checked' : '')+' />';
						new_op 	+= '<label for="'+(data.id + '-' +op.value)+'" class="lumise-cart-option-label">'+
										op.title.replace(/\</g, '&lt;').replace(/\>/g, '&gt;')+
										'<em class="check"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="12px" height="14px" viewBox="0 0 12 13" xml:space="preserve"><path fill="#4DB6AC" d="M0.211,6.663C0.119,6.571,0.074,6.435,0.074,6.343c0-0.091,0.045-0.229,0.137-0.32l0.64-0.64 c0.184-0.183,0.458-0.183,0.64,0L1.538,5.43l2.515,2.697c0.092,0.094,0.229,0.094,0.321,0l6.13-6.358l0.032-0.026l0.039-0.037 c0.186-0.183,0.432-0.12,0.613,0.063l0.64,0.642c0.183,0.184,0.183,0.457,0,0.64l0,0l-7.317,7.592 c-0.093,0.092-0.184,0.139-0.321,0.139s-0.228-0.047-0.319-0.139L0.302,6.8L0.211,6.663z"/></svg></em>'+
										'</label>';
									
						new_op 	+= '<em></em></div>';
										
						wrp.append(new_op);
						
					});
					
					return wrp;
					
EOF
			),

			'radio' => array(
				'label' => $lumise->lang('Radio checkbox'),
				'render' => <<<EOF
					
					var wrp = $('<div class="lumise_radios"></div>');
					
					if (!data.value)
						data.value = [];
					else if (typeof data.value == 'string')
						data.value = data.value.split(',');
					
					data.values.map(function (op){
						
						new_op 	= $('<div class="lumise-radio">'+
									'<input type="radio" class="lumise-cart-param" name="'+data.id+'" value="'+op.value+'" id="'+data.id+' '+op.value+'"'+(data.value.indexOf(op.value) > -1 ? ' checked' : '')+' />'+
				                	'<label class="lumise-cart-option-label" for="'+data.id+' '+op.value+'">'+op.title+' <em class="check"></em></label>'+
									'<em class="lumise-cart-option-desc"></em>'+
								'</div>');
						
						wrp.append(new_op);
						
					});
						
					return wrp;
								
EOF
			),
			
			'quantity' => array(
				'label' => $lumise->lang('Package'),
				'unique' => true,
				'render' => <<<EOF
					
					if (data.value === undefined)
						data.value = 1;
					
					if (typeof data.values == 'object' && data.values.length > 1) {
						var el = '<select name="'+data.id+'" class="lumise-cart-param" required>';
					
						data.values.map(function (op){
							el += '<option value="'+encodeURI(op.value)+'"'+(data.value == op.value ? ' selected' : '')+'>'+op.title+'</option>';
						});
						
						el += '</select>';
						
						return $(el);		
					}
					
					var new_op = $('<div class="lumise-cart-field-quantity">\
								<em data-action="minus"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 491.858 491.858" xml:space="preserve" width="10px" height="10px"><path d="M465.167,211.613H240.21H26.69c-8.424,0-26.69,11.439-26.69,34.316s18.267,34.316,26.69,34.316h213.52h224.959    c8.421,0,26.689-11.439,26.689-34.316S473.59,211.613,465.167,211.613z" fill="#888"/></svg></em>\
								<em class="lumise-cart-field-value">\
									<input type="number" min="0" step="1" class="lumise-cart-param" name="'+data.id+'" value="'+(data.value !== '' ? data.value : 1)+'"/>\
								</em>\
								<em data-action="plus"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http d://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 491.86 491.86" xml:space="preserve" width="10px" height="10px"><path d="M465.167,211.614H280.245V26.691c0-8.424-11.439-26.69-34.316-26.69s-34.316,18.267-34.316,26.69v184.924H26.69    C18.267,211.614,0,223.053,0,245.929s18.267,34.316,26.69,34.316h184.924v184.924c0,8.422,11.438,26.69,34.316,26.69    s34.316-18.268,34.316-26.69V280.245H465.17c8.422,0,26.69-11.438,26.69-34.316S473.59,211.614,465.167,211.614z" fill="#888"/></svg></em>\
							</div>');
						
						new_op.find('input.lumise-cart-param').on('input', function (){
							var val = parseInt(this.value);
							if (isNaN(val) || val === '' || val < 1)
								$(this).val(1).select();
							else $(this).val(val);
						});
						
						new_op.find('em[data-action]').on('click', function (){
							
							var action = $(this).data('action'),
								wrp = $(this).closest('.lumise-cart-field-quantity'),
								inp = wrp.find('input.lumise-cart-param'),
								val = parseInt(inp.val());

							switch (action) {
								case 'minus':
									val--;
									break;

								default:
									val++;
							}
							
							if (val < 1)
								val = 1;
								
							inp.val(val).trigger('change');
							
						});
					
					return new_op;
					
EOF
			)
			
		);
	}
}