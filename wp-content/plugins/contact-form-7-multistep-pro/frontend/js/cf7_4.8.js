( function( $ ) {

	'use strict';

	if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
		return;
	}

	wpcf7 = $.extend( {
		cached: 0,
		inputs: []
	}, wpcf7 );

	$( function() {
		

	wpcf7.getId = function( form ) {
		return parseInt( $( 'input[name="_wpcf7"]', form ).val(), 10 );
	};

	wpcf7.initForm = function( form ) {
		var $form = $( form );

		wpcf7.setStatus( $form, 'init' );

		$form.submit( function( event ) {
			if ( ! wpcf7.supportHtml5.placeholder ) {
				$( '[placeholder].placeheld', $form ).each( function( i, n ) {
					$( n ).val( '' ).removeClass( 'placeheld' );
				} );
			}

			if ( typeof window.FormData === 'function' ) {
				wpcf7.submit( $form );
				event.preventDefault();
			}
		} );

		$( '.wpcf7-submit', $form ).after( '<span class="ajax-loader"></span>' );

		wpcf7.toggleSubmit( $form );

		$form.on( 'click', '.wpcf7-acceptance', function() {
			wpcf7.toggleSubmit( $form );
		} );

		// Exclusive Checkbox
		$( '.wpcf7-exclusive-checkbox', $form ).on( 'click', 'input:checkbox', function() {
			var name = $( this ).attr( 'name' );
			$form.find( 'input:checkbox[name="' + name + '"]' ).not( this ).prop( 'checked', false );
		} );

		// Free Text Option for Checkboxes and Radio Buttons
		$( '.wpcf7-list-item.has-free-text', $form ).each( function() {
			var $freetext = $( ':input.wpcf7-free-text', this );
			var $wrap = $( this ).closest( '.wpcf7-form-control' );

			if ( $( ':checkbox, :radio', this ).is( ':checked' ) ) {
				$freetext.prop( 'disabled', false );
			} else {
				$freetext.prop( 'disabled', true );
			}

			$wrap.on( 'change', ':checkbox, :radio', function() {
				var $cb = $( '.has-free-text', $wrap ).find( ':checkbox, :radio' );

				if ( $cb.is( ':checked' ) ) {
					$freetext.prop( 'disabled', false ).focus();
				} else {
					$freetext.prop( 'disabled', true );
				}
			} );
		} );

		// Placeholder Fallback
		if ( ! wpcf7.supportHtml5.placeholder ) {
			$( '[placeholder]', $form ).each( function() {
				$( this ).val( $( this ).attr( 'placeholder' ) );
				$( this ).addClass( 'placeheld' );

				$( this ).focus( function() {
					if ( $( this ).hasClass( 'placeheld' ) ) {
						$( this ).val( '' ).removeClass( 'placeheld' );
					}
				} );

				$( this ).blur( function() {
					if ( '' === $( this ).val() ) {
						$( this ).val( $( this ).attr( 'placeholder' ) );
						$( this ).addClass( 'placeheld' );
					}
				} );
			} );
		}

		if ( wpcf7.jqueryUi && ! wpcf7.supportHtml5.date ) {
			$form.find( 'input.wpcf7-date[type="date"]' ).each( function() {
				$( this ).datepicker( {
					dateFormat: 'yy-mm-dd',
					minDate: new Date( $( this ).attr( 'min' ) ),
					maxDate: new Date( $( this ).attr( 'max' ) )
				} );
			} );
		}

		if ( wpcf7.jqueryUi && ! wpcf7.supportHtml5.number ) {
			$form.find( 'input.wpcf7-number[type="number"]' ).each( function() {
				$( this ).spinner( {
					min: $( this ).attr( 'min' ),
					max: $( this ).attr( 'max' ),
					step: $( this ).attr( 'step' )
				} );
			} );
		}

		// Character Count
		wpcf7.resetCounter( $form );

		// URL Input Correction
		$form.on( 'change', '.wpcf7-validates-as-url', function() {
			var val = $.trim( $( this ).val() );

			if ( val
			&& ! val.match( /^[a-z][a-z0-9.+-]*:/i )
			&& -1 !== val.indexOf( '.' ) ) {
				val = val.replace( /^\/+/, '' );
				val = 'http://' + val;
			}

			$( this ).val( val );
		} );
	};

	wpcf7.submit = function( form ) {
		if ( typeof window.FormData !== 'function' ) {
			return;
		}

		var $form = $( form );

//-----------------------------------------------------------------------Custom----------------------------------------------------------
if(  $form.find(".container-cf7-steps").length  > 0 ) {

		var step_comfirm_html = '<div class="cf7-container-step-confirm">';
		var cout_tab = $(".cf7-display-steps-container li", $form).length - 2;

		$( ".cf7-display-steps-container li" ,$form).each(function( index ) {
			if( index > cout_tab ){
		  		return;
		  	}
		  	step_comfirm_html +='<div class="cf7-step-confirm-title">'+ $( this ).text() +'</div>';
		  	var tab_name = $(this).data("tab");
		  	var name_tab = [];
		  	$form.find( tab_name + " input," + tab_name + " select," + tab_name +" textarea" ).each(function( index, joc ) {

		  		if ($(this).attr("name") != "" && typeof $(this).attr("name") != 'undefined') { 
		  			var name = $(this).attr("name").replace("[]", "");
			  		if( name_tab.indexOf(name) < 0 ) {
			  			name_tab.push(name);
			  			var value = cf7_step_confirm[name];
				  		if(value  === undefined || value == "" ) {
				  			value = name
				  		}
				  		var type =$(this).attr("type");
				  		var data ="";
				  		if( type == "radio" ){
				  				var chkArray = [];
				  				$("input[name="+name+"]:checked").each(function() {
									chkArray.push($(this).val());
								});
								data = chkArray.join(',') ;
				  		} else if(type == "checkbox"){
				  				var chkArray = [];
				  				$('input[name="'+name+'[]"]:checked').each(function() {
									chkArray.push($(this).val());
								});
								data = chkArray.join(',') ;
				  		} else{
				  			data = $(this).val();
				  		}
				  		if(data.trim() != "") { 
				  			if( name.search("repeater") !== 0 ) {
								step_comfirm_html +='<div class="cf7-step-confirm-item"><div class="cf7-step-confirm-name">'+ value+': </div><div class="cf7-step-confirm-value">'+ data +'</div></div>';
							}
				  		}
			  		} 
		  		}		
		  	})
		  	
		  	
		});
		//console.log(1);
			step_comfirm_html +="</div>";
			$(".cf7-data-confirm",$form).html(step_comfirm_html);
			
			var tab_current = $( '.wpcf7_check_tab', $form ).val();
	            $(".cf7-tab-"+tab_current + " .multistep-nav-right .ajax-loader", $form).removeClass("hidden");
			$( '[placeholder].placeheld', $form ).each( function( i, n ) {
				$( n ).val( '' );
			} );

			wpcf7.clearResponse( $form );
			$( '.ajax-loader', $form ).addClass( 'is-active' );

			if ( typeof window.FormData !== 'function' ) {
				return;
			}

      var formData = new FormData( $form.get( 0 ) );
      
      var detail = {
        id: $form.closest( 'div.wpcf7' ).attr( 'id' ),
        status: 'init',
        inputs: [],
        formData: formData
      };
  
      $.each( $form.serializeArray(), function( i, field ) {
        if ( '_wpcf7' == field.name ) {
          detail.contactFormId = field.value;
        } else if ( '_wpcf7_version' == field.name ) {
          detail.pluginVersion = field.value;
        } else if ( '_wpcf7_locale' == field.name ) {
          detail.contactFormLocale = field.value;
        } else if ( '_wpcf7_unit_tag' == field.name ) {
          detail.unitTag = field.value;
        } else if ( '_wpcf7_container_post' == field.name ) {
          detail.containerPostId = field.value;
        } else if ( field.name.match( /^_/ ) ) {
          // do nothing
        } else {
          detail.inputs.push( field );
        }
      } );
  
      wpcf7.triggerEvent( $form.closest( 'div.wpcf7' ), 'beforesubmit', detail );

			var ajaxSuccess = function( data, status, xhr, $form ) {
				detail.id = $( data.into ).attr( 'id' );
			  detail.status = data.status;
        detail.apiResponse = data;
        console.log('ajaxSuccess!!!');

				var $message = $( '.wpcf7-response-output', $form );

				switch ( data.status ) {
					case 'validation_failed':
						var error_tab = false;
	            		var tab_current = parseInt( $(".wpcf7_check_tab", $form).val() );
	            		// ver <  5.3 data.invalidFields
						$.each( data.invalid_fields, function( i, n ) {

							$( n.into, $form ).each( function() {
								if( $(".cf7-tab-"+tab_current +" " +n.into ,  $form ).length > 0 ) {
										error_tab = true;
										wpcf7.notValidTip( this, n.message );
										$form.find( ".cf7-tab-"+tab_current +" "+n.into,  $form ).find( '.wpcf7-form-control' ).addClass( 'wpcf7-not-valid' );
							    		$form.find( ".cf7-tab-"+tab_current +" "+n.into,  $form ).find( '[aria-invalid]' ).attr( 'aria-invalid', 'true' );
								 }
								
							} );
						} );
						 if( !error_tab ) {
			                /*
			                * Next tab
			                */
			               
			                var next_tab = tab_current+1;
			                $(".cf7-tab", $form).addClass("hidden");
			                $(".cf7-tab-"+next_tab,  $form).removeClass("hidden");
			                $(".wpcf7_check_tab", $form).val( next_tab  ).change();
			                $(".wpcf7-response-output", $form).addClass("hidden");
			                $(".cf7-display-steps-container li", $form).removeClass("active");
			                $(".cf7-display-steps-container .cf7-steps-"+next_tab, $form).addClass("active");
			                for(var i=1;i<next_tab;i++){
			                    $(".cf7-display-steps-container li.cf7-steps-"+i,  $form).addClass("enabled");
			                }
			                $(".cf7-tab-"+tab_current + " .multistep-nav-right .ajax-loader",  $form).addClass("hidden");
			                var top = $('.container-multistep-header', $form).offset().top - 200;

       					 $('html, body').animate({scrollTop : top},800);
			                // var tab_final = next_tab + 1;
			                if( next_tab == $(".multistep_total", $form).val() ){
			                	$('.wpcf7-acceptance input:checkbox',$form).each(function () {
						               $(this).prop( "checked", false );
						        });
			                    $(".multistep-check input", $form).val("ok").change();
			                }
			            }else{
			                $(".wpcf7-response-output", $form).removeClass("hidden");
			            }	


						$message.addClass( 'wpcf7-validation-errors' );
						$form.addClass( 'invalid' );

						wpcf7.triggerEvent( data.into, 'invalid', detail );
						break;
					case 'spam':
						$message.addClass( 'wpcf7-spam-blocked' );
						$form.addClass( 'spam' );

						$( '[name="g-recaptcha-response"]', $form ).each( function() {
							if ( '' === $( this ).val() ) {
								var $recaptcha = $( this ).closest( '.wpcf7-form-control-wrap' );
								wpcf7.notValidTip( $recaptcha, wpcf7.recaptcha.messages.empty );
							}
						} );

						wpcf7.triggerEvent( data.into, 'spam', detail );
						break;
					case 'mail_sent':
						$message.addClass( 'wpcf7-mail-sent-ok' );
						$form.addClass( 'sent' );

						if ( data.onSentOk ) {
							$.each( data.onSentOk, function( i, n ) { eval( n ) } );
						}

						wpcf7.triggerEvent( data.into, 'mailsent', detail );
						$(".cf7-display-steps-container li", $form).removeClass("enabled").removeClass("active");
			            $(".cf7-steps-1", $form).addClass("active");
			            $(".cf7-tab", $form).addClass("hidden");
			            $(".cf7-tab-1", $form).removeClass("hidden");
			            $(".wpcf7_check_tab", $form).val(1);
			            $(".wpcf7-response-output", $form).removeClass("hidden");
			            var top = $('.container-multistep-header', $form).offset().top - 200;

       					 $('html, body').animate({scrollTop : top},800);
			            if(  $form.find(".cf7-thankyou-page-success").length  > 0 ) { 
			            	var check = $form.find('.cf7-thankyou-page-success').val();
							if( check != 0 ){
								window.location.href = check;
							}
			            }	
			            // Paypal
			            if ( typeof cf7_paypal === 'undefined' || cf7_paypal === null ) {
							
						}else{
							
						}
			            if(  $form.find(".cf7-thankyou-page-success").length  > 0 ) { 
			            	var check = $form.find('.cf7-thankyou-page-success').val();
							if( check != 0 ){
								window.location.href = check;
							}
			            }

						break;
					case 'mail_failed':
					case 'acceptance_missing':
					default:

						$message.addClass( 'wpcf7-mail-sent-ng' );
						$form.addClass( 'failed' );

						wpcf7.triggerEvent( data.into, 'mailfailed', detail );
						if(  $form.find(".cf7-thankyou-page-failed").length  > 0 ) { 
			            	var check = $form.find('.cf7-thankyou-page-failed').val();
								if( check != 0 ){
									window.location.href = check;
								}
			            }
				}

				wpcf7.refill( $form, data );

				if ( data.onSubmit ) {
					$.each( data.onSubmit, function( i, n ) { eval( n ) } );
				}

				wpcf7.triggerEvent( data.into, 'submit', detail );

				if ( 'mail_sent' == data.status ) {
					$form.each( function() {
						this.reset();
					} );
				}

				$form.find( '[placeholder].placeheld' ).each( function( i, n ) {
					$( n ).val( $( n ).attr( 'placeholder' ) );
				} );

				$message.append( data.message ).slideDown( 'fast' );
				$message.attr( 'role', 'alert' );

				$( '.screen-reader-response', $form.closest( '.wpcf7' ) ).each( function() {
					var $response = $( this );
					$response.html( '' ).attr( 'role', '' ).append( data.message );

					if ( data.invalidFields ) {
						var $invalids = $( '<ul></ul>' );

						$.each( data.invalidFields, function( i, n ) {
							if ( n.idref ) {
								var $li = $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', '#' + n.idref ).append( n.message ) );
							} else {
								var $li = $( '<li></li>' ).append( n.message );
							}

							$invalids.append( $li );
						} );

						$response.append( $invalids );
					}

					$response.attr( 'role', 'alert' ).focus();
				} );
			};

			$.ajax( {
				type: 'POST',
				url: wpcf7.apiSettings.root + wpcf7.apiSettings.namespace +
					'/contact-forms/' + wpcf7.getId( $form ) + '/feedback',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false
			} ).done( function( data, status, xhr ) {
				ajaxSuccess( data, status, xhr, $form );
				$( '.ajax-loader', $form ).removeClass( 'is-active' );
			} ).fail( function( xhr, status, error ) {
				var $e = $( '<div class="ajax-error"></div>' ).text( error.message );
				$form.after( $e );
			} );

//----------------------------------------------------------------------------End custom---------------------------------------------------------
		}else {

			
			$( '.ajax-loader', $form ).addClass( 'is-active' );
		wpcf7.clearResponse( $form );

		var formData = new FormData( $form.get( 0 ) );

		var detail = {
			id: $form.closest( 'div.wpcf7' ).attr( 'id' ),
			status: 'init',
			inputs: [],
			formData: formData
		};

		$.each( $form.serializeArray(), function( i, field ) {
			if ( '_wpcf7' == field.name ) {
				detail.contactFormId = field.value;
			} else if ( '_wpcf7_version' == field.name ) {
				detail.pluginVersion = field.value;
			} else if ( '_wpcf7_locale' == field.name ) {
				detail.contactFormLocale = field.value;
			} else if ( '_wpcf7_unit_tag' == field.name ) {
				detail.unitTag = field.value;
			} else if ( '_wpcf7_container_post' == field.name ) {
				detail.containerPostId = field.value;
			} else if ( field.name.match( /^_/ ) ) {
				// do nothing
			} else {
				detail.inputs.push( field );
			}
		} );

		wpcf7.triggerEvent( $form.closest( 'div.wpcf7' ), 'beforesubmit', detail );

		var ajaxSuccess = function( data, status, xhr, $form ) {
			detail.id = $( data.into ).attr( 'id' );
			detail.status = data.status;
			detail.apiResponse = data;

			switch ( data.status ) {
				case 'init':
					wpcf7.setStatus( $form, 'init' );
					break;
				case 'validation_failed':
					$.each( data.invalid_fields, function( i, n ) {
						$( n.into, $form ).each( function() {
							wpcf7.notValidTip( this, n.message );
							$( '.wpcf7-form-control', this ).addClass( 'wpcf7-not-valid' );
							$( '.wpcf7-form-control', this ).attr(
								'aria-describedby',
								n.error_id
							);
							$( '[aria-invalid]', this ).attr( 'aria-invalid', 'true' );
						} );
					} );

					wpcf7.setStatus( $form, 'invalid' );
					wpcf7.triggerEvent( data.into, 'invalid', detail );
					break;
				case 'acceptance_missing':
					wpcf7.setStatus( $form, 'unaccepted' );
					wpcf7.triggerEvent( data.into, 'unaccepted', detail );
					break;
				case 'spam':
					wpcf7.setStatus( $form, 'spam' );
					wpcf7.triggerEvent( data.into, 'spam', detail );
					break;
				case 'aborted':
					wpcf7.setStatus( $form, 'aborted' );
					wpcf7.triggerEvent( data.into, 'aborted', detail );
					break;
				case 'mail_sent':
					wpcf7.setStatus( $form, 'sent' );
					wpcf7.triggerEvent( data.into, 'mailsent', detail );
					break;
				case 'mail_failed':
					wpcf7.setStatus( $form, 'failed' );
					wpcf7.triggerEvent( data.into, 'mailfailed', detail );
					break;
				default:
					wpcf7.setStatus( $form,
						'custom-' + data.status.replace( /[^0-9a-z]+/i, '-' )
					);
			}

			wpcf7.refill( $form, data );

			wpcf7.triggerEvent( data.into, 'submit', detail );

			if ( 'mail_sent' == data.status ) {
				$form.each( function() {
					this.reset();
				} );

				wpcf7.toggleSubmit( $form );
				wpcf7.resetCounter( $form );
			}

			if ( ! wpcf7.supportHtml5.placeholder ) {
				$form.find( '[placeholder].placeheld' ).each( function( i, n ) {
					$( n ).val( $( n ).attr( 'placeholder' ) );
				} );
			}

			$( '.wpcf7-response-output', $form )
				.html( '' ).append( data.message ).slideDown( 'fast' );

			$( '.screen-reader-response', $form.closest( '.wpcf7' ) ).each( function() {
				var $response = $( this );
				$( '[role="status"]', $response ).html( data.message );

				if ( data.invalid_fields ) {
					$.each( data.invalid_fields, function( i, n ) {
						if ( n.idref ) {
							var $li = $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', '#' + n.idref ).append( n.message ) );
						} else {
							var $li = $( '<li></li>' ).append( n.message );
						}

						$li.attr( 'id', n.error_id );

						$( 'ul', $response ).append( $li );
					} );
				}
			} );

			if ( data.posted_data_hash ) {
				$form.find( 'input[name="_wpcf7_posted_data_hash"]' ).first()
					.val( data.posted_data_hash );
			}
		};

		$.ajax( {
			type: 'POST',
			url: wpcf7.apiSettings.getRoute(
				'/contact-forms/' + wpcf7.getId( $form ) + '/feedback' ),
			data: formData,
			dataType: 'json',
			processData: false,
			contentType: false
		} ).done( function( data, status, xhr ) {
			ajaxSuccess( data, status, xhr, $form );
			$( '.ajax-loader', $form ).removeClass( 'is-active' );
		} ).fail( function( xhr, status, error ) {
			var $e = $( '<div class="ajax-error"></div>' ).text( error.message );
			$form.after( $e );
		} );


		}
		/*
		End custom
		 */
	};

	wpcf7.triggerEvent = function( target, name, detail ) {
		var event = new CustomEvent( 'wpcf7' + name, {
			bubbles: true,
			detail: detail
		} );

		$( target ).get( 0 ).dispatchEvent( event );
	};

	wpcf7.setStatus = function( form, status ) {
		var $form = $( form );
		var prevStatus = $form.attr( 'data-status' );

		$form.data( 'status', status );
		$form.addClass( status );
		$form.attr( 'data-status', status );

		if ( prevStatus && prevStatus !== status ) {
			$form.removeClass( prevStatus );
		}
	}

	wpcf7.toggleSubmit = function( form, state ) {
		var $form = $( form );
		var $submit = $( 'input:submit', $form );

		if ( typeof state !== 'undefined' ) {
			$submit.prop( 'disabled', ! state );
			return;
		}

		if ( $form.hasClass( 'wpcf7-acceptance-as-validation' ) ) {
			return;
		}

		$submit.prop( 'disabled', false );

		$( '.wpcf7-acceptance', $form ).each( function() {
			var $span = $( this );
			var $input = $( 'input:checkbox', $span );

			if ( ! $span.hasClass( 'optional' ) ) {
				if ( $span.hasClass( 'invert' ) && $input.is( ':checked' )
				|| ! $span.hasClass( 'invert' ) && ! $input.is( ':checked' ) ) {
					$submit.prop( 'disabled', true );
					return false;
				}
			}
		} );
	};

	wpcf7.resetCounter = function( form ) {
		var $form = $( form );

		$( '.wpcf7-character-count', $form ).each( function() {
			var $count = $( this );
			var name = $count.attr( 'data-target-name' );
			var down = $count.hasClass( 'down' );
			var starting = parseInt( $count.attr( 'data-starting-value' ), 10 );
			var maximum = parseInt( $count.attr( 'data-maximum-value' ), 10 );
			var minimum = parseInt( $count.attr( 'data-minimum-value' ), 10 );

			var updateCount = function( target ) {
				var $target = $( target );
				var length = $target.val().length;
				var count = down ? starting - length : length;
				$count.attr( 'data-current-value', count );
				$count.text( count );

				if ( maximum && maximum < length ) {
					$count.addClass( 'too-long' );
				} else {
					$count.removeClass( 'too-long' );
				}

				if ( minimum && length < minimum ) {
					$count.addClass( 'too-short' );
				} else {
					$count.removeClass( 'too-short' );
				}
			};

			$( ':input[name="' + name + '"]', $form ).each( function() {
				updateCount( this );

				$( this ).keyup( function() {
					updateCount( this );
				} );
			} );
		} );
	};

	wpcf7.notValidTip = function( target, message ) {
		var $target = $( target );
		$( '.wpcf7-not-valid-tip', $target ).remove();

		$( '<span></span>' ).attr( {
			'class': 'wpcf7-not-valid-tip',
			'aria-hidden': 'true',
		} ).text( message ).appendTo( $target );

		if ( $target.is( '.use-floating-validation-tip *' ) ) {
			var fadeOut = function( target ) {
				$( target ).not( ':hidden' ).animate( {
					opacity: 0
				}, 'fast', function() {
					$( this ).css( { 'z-index': -100 } );
				} );
			};

			$target.on( 'mouseover', '.wpcf7-not-valid-tip', function() {
				fadeOut( this );
			} );

			$target.on( 'focus', ':input', function() {
				fadeOut( $( '.wpcf7-not-valid-tip', $target ) );
			} );
		}
	};

	wpcf7.refill = function( form, data ) {
		var $form = $( form );

		var refillCaptcha = function( $form, items ) {
			$.each( items, function( i, n ) {
				$form.find( ':input[name="' + i + '"]' ).val( '' );
				$form.find( 'img.wpcf7-captcha-' + i ).attr( 'src', n );
				var match = /([0-9]+)\.(png|gif|jpeg)$/.exec( n );
				$form.find( 'input:hidden[name="_wpcf7_captcha_challenge_' + i + '"]' ).attr( 'value', match[ 1 ] );
			} );
		};

		var refillQuiz = function( $form, items ) {
			$.each( items, function( i, n ) {
				$form.find( ':input[name="' + i + '"]' ).val( '' );
				$form.find( ':input[name="' + i + '"]' ).siblings( 'span.wpcf7-quiz-label' ).text( n[ 0 ] );
				$form.find( 'input:hidden[name="_wpcf7_quiz_answer_' + i + '"]' ).attr( 'value', n[ 1 ] );
			} );
		};

		if ( typeof data === 'undefined' ) {
			$.ajax( {
				type: 'GET',
				url: wpcf7.apiSettings.getRoute(
					'/contact-forms/' + wpcf7.getId( $form ) + '/refill' ),
				beforeSend: function( xhr ) {
					var nonce = $form.find( ':input[name="_wpnonce"]' ).val();

					if ( nonce ) {
						xhr.setRequestHeader( 'X-WP-Nonce', nonce );
					}
				},
				dataType: 'json'
			} ).done( function( data, status, xhr ) {
				if ( data.captcha ) {
					refillCaptcha( $form, data.captcha );
				}

				if ( data.quiz ) {
					refillQuiz( $form, data.quiz );
				}
			} );

		} else {
			if ( data.captcha ) {
				refillCaptcha( $form, data.captcha );
			}

			if ( data.quiz ) {
				refillQuiz( $form, data.quiz );
			}
		}
	};

	wpcf7.clearResponse = function( form ) {
		var $form = $( form );

		$form.siblings( '.screen-reader-response' ).each( function() {
			$( '[role="status"]', this ).html( '' );
			$( 'ul', this ).html( '' );
		} );

		$( '.wpcf7-not-valid-tip', $form ).remove();
		$( '[aria-invalid]', $form ).attr( 'aria-invalid', 'false' );
		$( '.wpcf7-form-control', $form ).removeClass( 'wpcf7-not-valid' );

		$( '.wpcf7-response-output', $form ).hide().empty();
	};

	wpcf7.apiSettings.getRoute = function( path ) {
		var url = wpcf7.apiSettings.root;

		url = url.replace(
			wpcf7.apiSettings.namespace,
			wpcf7.apiSettings.namespace + path );

		return url;
  };
  
  wpcf7.supportHtml5 = ( function() {
    var features = {};
    var input = document.createElement( 'input' );

    features.placeholder = 'placeholder' in input;

    var inputTypes = [ 'email', 'url', 'tel', 'number', 'range', 'date' ];

    $.each( inputTypes, function( index, value ) {
      input.setAttribute( 'type', value );
      features[ value ] = input.type !== 'text';
    } );

    return features;
  } )();

  $( 'div.wpcf7 > form' ).each( function() {
    var $form = $( this );
    wpcf7.initForm( $form );

    if ( wpcf7.cached ) {
      wpcf7.refill( $form );
    }
  } );
} );

} )( jQuery );

/*
 * Polyfill for Internet Explorer
 * See https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
 */
( function () {
	if ( typeof window.CustomEvent === "function" ) return false;

	function CustomEvent ( event, params ) {
		params = params || { bubbles: false, cancelable: false, detail: undefined };
		var evt = document.createEvent( 'CustomEvent' );
		evt.initCustomEvent( event,
			params.bubbles, params.cancelable, params.detail );
		return evt;
	}

	CustomEvent.prototype = window.Event.prototype;

	window.CustomEvent = CustomEvent;
} )();
