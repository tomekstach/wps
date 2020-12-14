( function( $ ) {
	'use strict';
	if ( typeof _wpcf7 === 'undefined' || _wpcf7 === null ) {
		return;
	}
	_wpcf7 = $.extend( {
		cached: 0,
		inputs: []
	}, _wpcf7 );
	$.fn.wpcf7InitForm = function() {
		this.ajaxForm( {
			beforeSubmit: function( arr, $form, options ) {
				$form.wpcf7ClearResponseOutput();
				$form.find( '[aria-invalid]' ).attr( 'aria-invalid', 'false' );
				$form.find( '.ajax-loader' ).addClass( 'is-active' );
				if(  $form.find(".container-cf7-steps").length  > 0 ) { 
						var tab_current = parseInt( $("#wpcf7_check_tab").val() );
                		$("#cf7-tab-"+tab_current + " .multistep-nav-right .ajax-loader").removeClass("hidden");
				}
                
				return true;
			},
			beforeSerialize: function( $form, options ) {
				$form.find( '[placeholder].placeheld' ).each( function( i, n ) {
					$( n ).val( '' );
				} );
				return true;
			},
			data: { '_wpcf7_is_ajax_call': 1 },
			dataType: 'json',
			success: $.wpcf7AjaxSuccess,
			error: function( xhr, status, error, $form ) {
				var e = $( '<div class="ajax-error"></div>' ).text( error.message );
				$form.after( e );
			}
		} );
		if ( _wpcf7.cached ) {
			this.wpcf7OnloadRefill();
		}
		this.wpcf7ToggleSubmit();
		this.find( '.wpcf7-submit' ).wpcf7AjaxLoader();
		this.find( '.wpcf7-acceptance' ).click( function() {
			$( this ).closest( 'form' ).wpcf7ToggleSubmit();
		} );
		this.find( '.wpcf7-exclusive-checkbox' ).wpcf7ExclusiveCheckbox();
		this.find( '.wpcf7-list-item.has-free-text' ).wpcf7ToggleCheckboxFreetext();
		this.find( '[placeholder]' ).wpcf7Placeholder();
		if ( _wpcf7.jqueryUi && ! _wpcf7.supportHtml5.date ) {
			this.find( 'input.wpcf7-date[type="date"]' ).each( function() {
				$( this ).datepicker( {
					dateFormat: 'yy-mm-dd',
					minDate: new Date( $( this ).attr( 'min' ) ),
					maxDate: new Date( $( this ).attr( 'max' ) )
				} );
			} );
		}
		if ( _wpcf7.jqueryUi && ! _wpcf7.supportHtml5.number ) {
			this.find( 'input.wpcf7-number[type="number"]' ).each( function() {
				$( this ).spinner( {
					min: $( this ).attr( 'min' ),
					max: $( this ).attr( 'max' ),
					step: $( this ).attr( 'step' )
				} );
			} );
		}
		this.find( '.wpcf7-character-count' ).wpcf7CharacterCount();
		this.find( '.wpcf7-validates-as-url' ).change( function() {
			$( this ).wpcf7NormalizeUrl();
		} );
		this.find( '.wpcf7-recaptcha' ).wpcf7Recaptcha();
	};
	$.wpcf7AjaxSuccess = function( data, status, xhr, $form ) {
	  // console.log(data);
		if ( ! $.isPlainObject( data ) || $.isEmptyObject( data ) ) {
			return;
		}
		_wpcf7.inputs = $form.serializeArray();
		var $responseOutput = $form.find( 'div.wpcf7-response-output' );
		$form.wpcf7ClearResponseOutput();
		$form.find( '.wpcf7-form-control' ).removeClass( 'wpcf7-not-valid' );
		$form.removeClass( 'invalid spam sent failed' );
		if ( data.captcha ) {
			$form.wpcf7RefillCaptcha( data.captcha );
		}
		if ( data.quiz ) {
			$form.wpcf7RefillQuiz( data.quiz );
		}


		if(  $form.find(".container-cf7-steps").length  > 0 ) { 
				$(".wpcf7-response-output").removeClass("hidden");

        		$(".multistep-nav .ajax-loader").addClass("hidden");
		}
        
		if ( data.invalids ) {
			if(  $form.find(".container-cf7-steps").length  > 0 ) {  
					var check_count = $form.find('.cf7-content-tab').length;
					console.log(check_count);
					if( check_count < 1 ) {
							$.each( data.invalids, function( i, n ) {
								$form.find( n.into ).wpcf7NotValidTip( n.message );
								$form.find( n.into ).find( '.wpcf7-form-control' ).addClass( 'wpcf7-not-valid' );
								$form.find( n.into ).find( '[aria-invalid]' ).attr( 'aria-invalid', 'true' );
							} );

							$responseOutput.addClass( 'wpcf7-validation-errors' );
							$form.addClass( 'invalid' );

							$( data.into ).wpcf7TriggerEvent( 'invalid' );
					}else{
					    var error_tab = false;
			            var tab_current = parseInt( $("#wpcf7_check_tab").val() );
						$.each( data.invalids, function( i, n ) {
						     /*
			                * Check tab
			                */
			                if( $("#cf7-tab-"+tab_current +" " +n.into  ).length > 0 ) {
			                    error_tab = true;
			                    $form.find( "#cf7-tab-"+tab_current +" "+ n.into ).wpcf7NotValidTip( n.message );
							    $form.find( "#cf7-tab-"+tab_current +" "+n.into ).find( '.wpcf7-form-control' ).addClass( 'wpcf7-not-valid' );
							    $form.find( "#cf7-tab-"+tab_current +" "+n.into ).find( '[aria-invalid]' ).attr( 'aria-invalid', 'true' );
			                }
						} );
			            if( !error_tab ) {
			                /*
			                * Next tab
			                */
			                var next_tab = tab_current+1;
			                $(".cf7-tab").addClass("hidden");
			                $("#cf7-tab-"+next_tab).removeClass("hidden");
			                $("#wpcf7_check_tab").val( next_tab  );
			                $(".wpcf7-response-output").addClass("hidden");
			                $(".cf7-display-steps-container li").removeClass("active");
			                $(".cf7-display-steps-container .cf7-steps-"+next_tab).addClass("active");
			                for(var i=1;i<next_tab;i++){
			                    $(".cf7-display-steps-container li.cf7-steps-"+i).addClass("enabled");
			                }
			                $("#cf7-tab-"+tab_current + " .multistep-nav-right .ajax-loader").addClass("hidden");
			                var top = $('.container-multistep-header').offset().top - 200;

		       					 $('html, body').animate({scrollTop : top},800);
			                // var tab_final = next_tab + 1;
			                if( next_tab == $("#multistep_total").val() ){
			                    $(".multistep-check input").val("ok");
			                }
			            }else{
			                $(".wpcf7-response-output").removeClass("hidden");
			            }
						$responseOutput.addClass( 'wpcf7-validation-errors' );
						$form.addClass( 'invalid' );
						$( data.into ).wpcf7TriggerEvent( 'invalid' );
					}
			}else{
					$.each( data.invalids, function( i, n ) {
						$form.find( n.into ).wpcf7NotValidTip( n.message );
						$form.find( n.into ).find( '.wpcf7-form-control' ).addClass( 'wpcf7-not-valid' );
						$form.find( n.into ).find( '[aria-invalid]' ).attr( 'aria-invalid', 'true' );
					} );

					$responseOutput.addClass( 'wpcf7-validation-errors' );
					$form.addClass( 'invalid' );

					$( data.into ).wpcf7TriggerEvent( 'invalid' );
			}

			
			
		} else if ( 1 == data.spam ) {
			$form.find( '[name="g-recaptcha-response"]' ).each( function() {
				if ( '' == $( this ).val() ) {
					var $recaptcha = $( this ).closest( '.wpcf7-form-control-wrap' );
					$recaptcha.wpcf7NotValidTip( _wpcf7.recaptcha.messages.empty );
				}
			} );
			$responseOutput.addClass( 'wpcf7-spam-blocked' );
			$form.addClass( 'spam' );
			$( data.into ).wpcf7TriggerEvent( 'spam' );
		} else if ( 1 == data.mailSent ) {
			if(  $form.find(".container-cf7-steps").length  > 0 ) { 
				$responseOutput.addClass( 'wpcf7-mail-sent-ok' );
				$form.addClass( 'sent' );
				if ( data.onSentOk ) {
					$.each( data.onSentOk, function( i, n ) { eval( n ) } );
				}
				$( data.into ).wpcf7TriggerEvent( 'mailsent' );
				var top = $('.container-multistep-header').offset().top - 200;
			 }else{
			 	$responseOutput.addClass( 'wpcf7-mail-sent-ok' );
			$form.addClass( 'sent' );

			if ( data.onSentOk ) {
				$.each( data.onSentOk, function( i, n ) { eval( n ) } );
			}

			$( data.into ).wpcf7TriggerEvent( 'mailsent' );
			}
			

        $('html, body').animate({scrollTop : top},800);
			if(  $form.find(".cf7-thankyou-page-success").length  > 0 ) { 
            	var check = $form.find('.cf7-thankyou-page-success').val();
				if( check != 0 ){
					window.location.href = check;
				}
            }	
		} else {
			$responseOutput.addClass( 'wpcf7-mail-sent-ng' );
			$form.addClass( 'failed' );
			$( data.into ).wpcf7TriggerEvent( 'mailfailed' );
			if(  $form.find(".cf7-thankyou-page-failed").length  > 0 ) { 
            	var check = $form.find('.cf7-thankyou-page-failed').val();
					if( check != 0 ){
						window.location.href = check;
					}
            }
		}
		if ( data.onSubmit ) {
			$.each( data.onSubmit, function( i, n ) { eval( n ) } );
		}
		$( data.into ).wpcf7TriggerEvent( 'submit' );
		if ( 1 == data.mailSent ) {
			if(  $form.find(".container-cf7-steps").length  > 0 ) {   
				$form.resetForm();
	            $(".cf7-display-steps-container li").removeClass("enabled").removeClass("active");
	            $(".cf7-steps-1").addClass("active");
	            $(".cf7-tab").addClass("hidden");
	            $("#cf7-tab-1").removeClass("hidden");
	            $("#wpcf7_check_tab").val(1);
			}else{
				$form.resetForm();
			}
			
		}
		$form.find( '[placeholder].placeheld' ).each( function( i, n ) {
			$( n ).val( $( n ).attr( 'placeholder' ) );
		} );
		$responseOutput.append( data.message ).slideDown( 'fast' );
		$responseOutput.attr( 'role', 'alert' );
		$.wpcf7UpdateScreenReaderResponse( $form, data );
	};
	$.fn.wpcf7TriggerEvent = function( name ) {
		return this.each( function() {
			var elmId = this.id;
			var inputs = _wpcf7.inputs;
			/* DOM event */
			var event = new CustomEvent( 'wpcf7' + name, {
				bubbles: true,
				detail: {
					id: elmId,
					inputs: inputs
				}
			} );
			this.dispatchEvent( event );
			/* jQuery event */
			$( this ).trigger( 'wpcf7:' + name );
			$( this ).trigger( name + '.wpcf7' ); // deprecated
		} );
	};
	$.fn.wpcf7ExclusiveCheckbox = function() {
		return this.find( 'input:checkbox' ).click( function() {
			var name = $( this ).attr( 'name' );
			$( this ).closest( 'form' ).find( 'input:checkbox[name="' + name + '"]' ).not( this ).prop( 'checked', false );
		} );
	};
	$.fn.wpcf7Placeholder = function() {
		if ( _wpcf7.supportHtml5.placeholder ) {
			return this;
		}
		return this.each( function() {
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
	};
	$.fn.wpcf7AjaxLoader = function() {
		return this.each( function() {
			$( this ).after( '<span class="ajax-loader"></span>' );
		} );
	};
	$.fn.wpcf7ToggleSubmit = function() {
		return this.each( function() {
			var form = $( this );
			if ( this.tagName.toLowerCase() != 'form' ) {
				form = $( this ).find( 'form' ).first();
			}
			if ( form.hasClass( 'wpcf7-acceptance-as-validation' ) ) {
				return;
			}
			var submit = form.find( 'input:submit' );
			if ( ! submit.length ) {
				return;
			}
			var acceptances = form.find( 'input:checkbox.wpcf7-acceptance' );
			if ( ! acceptances.length ) {
				return;
			}
			submit.removeAttr( 'disabled' );
			acceptances.each( function( i, n ) {
				n = $( n );
				if ( n.hasClass( 'wpcf7-invert' ) && n.is( ':checked' )
						|| ! n.hasClass( 'wpcf7-invert' ) && ! n.is( ':checked' ) ) {
					submit.attr( 'disabled', 'disabled' );
				}
			} );
		} );
	};
	$.fn.wpcf7ToggleCheckboxFreetext = function() {
		return this.each( function() {
			var $wrap = $( this ).closest( '.wpcf7-form-control' );
			if ( $( this ).find( ':checkbox, :radio' ).is( ':checked' ) ) {
				$( this ).find( ':input.wpcf7-free-text' ).prop( 'disabled', false );
			} else {
				$( this ).find( ':input.wpcf7-free-text' ).prop( 'disabled', true );
			}
			$wrap.find( ':checkbox, :radio' ).change( function() {
				var $cb = $( '.has-free-text', $wrap ).find( ':checkbox, :radio' );
				var $freetext = $( ':input.wpcf7-free-text', $wrap );
				if ( $cb.is( ':checked' ) ) {
					$freetext.prop( 'disabled', false ).focus();
				} else {
					$freetext.prop( 'disabled', true );
				}
			} );
		} );
	};
	$.fn.wpcf7CharacterCount = function() {
		return this.each( function() {
			var $count = $( this );
			var name = $count.attr( 'data-target-name' );
			var down = $count.hasClass( 'down' );
			var starting = parseInt( $count.attr( 'data-starting-value' ), 10 );
			var maximum = parseInt( $count.attr( 'data-maximum-value' ), 10 );
			var minimum = parseInt( $count.attr( 'data-minimum-value' ), 10 );
			var updateCount = function( $target ) {
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
			$count.closest( 'form' ).find( ':input[name="' + name + '"]' ).each( function() {
				updateCount( $( this ) );
				$( this ).keyup( function() {
					updateCount( $( this ) );
				} );
			} );
		} );
	};
	$.fn.wpcf7NormalizeUrl = function() {
		return this.each( function() {
			var val = $.trim( $( this ).val() );
			// check the scheme part
			if ( val && ! val.match( /^[a-z][a-z0-9.+-]*:/i ) ) {
				val = val.replace( /^\/+/, '' );
				val = 'http://' + val;
			}
			$( this ).val( val );
		} );
	};
	$.fn.wpcf7NotValidTip = function( message ) {
		return this.each( function() {
			var $into = $( this );
			$into.find( 'span.wpcf7-not-valid-tip' ).remove();
			$into.append( '<span role="alert" class="wpcf7-not-valid-tip">' + message + '</span>' );
			if ( $into.is( '.use-floating-validation-tip *' ) ) {
				$( '.wpcf7-not-valid-tip', $into ).mouseover( function() {
					$( this ).wpcf7FadeOut();
				} );
				$( ':input', $into ).focus( function() {
					$( '.wpcf7-not-valid-tip', $into ).not( ':hidden' ).wpcf7FadeOut();
				} );
			}
		} );
	};
	$.fn.wpcf7FadeOut = function() {
		return this.each( function() {
			$( this ).animate( {
				opacity: 0
			}, 'fast', function() {
				$( this ).css( { 'z-index': -100 } );
			} );
		} );
	};
	$.fn.wpcf7OnloadRefill = function() {
		return this.each( function() {
			var url = $( this ).attr( 'action' );
			if ( 0 < url.indexOf( '#' ) ) {
				url = url.substr( 0, url.indexOf( '#' ) );
			}
			var id = $( this ).find( 'input[name="_wpcf7"]' ).val();
			var unitTag = $( this ).find( 'input[name="_wpcf7_unit_tag"]' ).val();
			$.getJSON( url,
				{ _wpcf7_is_ajax_call: 1, _wpcf7: id, _wpcf7_request_ver: $.now() },
				function( data ) {
					if ( data && data.captcha ) {
						$( '#' + unitTag ).wpcf7RefillCaptcha( data.captcha );
					}
					if ( data && data.quiz ) {
						$( '#' + unitTag ).wpcf7RefillQuiz( data.quiz );
					}
				}
			);
		} );
	};
	$.fn.wpcf7RefillCaptcha = function( captcha ) {
		return this.each( function() {
			var form = $( this );
			$.each( captcha, function( i, n ) {
				form.find( ':input[name="' + i + '"]' ).clearFields();
				form.find( 'img.wpcf7-captcha-' + i ).attr( 'src', n );
				var match = /([0-9]+)\.(png|gif|jpeg)$/.exec( n );
				form.find( 'input:hidden[name="_wpcf7_captcha_challenge_' + i + '"]' ).attr( 'value', match[ 1 ] );
			} );
		} );
	};
	$.fn.wpcf7RefillQuiz = function( quiz ) {
		return this.each( function() {
			var form = $( this );
			$.each( quiz, function( i, n ) {
				form.find( ':input[name="' + i + '"]' ).clearFields();
				form.find( ':input[name="' + i + '"]' ).siblings( 'span.wpcf7-quiz-label' ).text( n[ 0 ] );
				form.find( 'input:hidden[name="_wpcf7_quiz_answer_' + i + '"]' ).attr( 'value', n[ 1 ] );
			} );
		} );
	};
	$.fn.wpcf7ClearResponseOutput = function() {
		return this.each( function() {
			$( this ).find( 'div.wpcf7-response-output' ).hide().empty().removeClass( 'wpcf7-mail-sent-ok wpcf7-mail-sent-ng wpcf7-validation-errors wpcf7-spam-blocked' ).removeAttr( 'role' );
			$( this ).find( 'span.wpcf7-not-valid-tip' ).remove();
			$( this ).find( '.ajax-loader' ).removeClass( 'is-active' );
		} );
	};
	$.fn.wpcf7Recaptcha = function() {
		return this.each( function() {
			var events = 'wpcf7:spam wpcf7:mailsent wpcf7:mailfailed';
			$( this ).closest( 'div.wpcf7' ).on( events, function( e ) {
				if ( recaptchaWidgets && grecaptcha ) {
					$.each( recaptchaWidgets, function( index, value ) {
						grecaptcha.reset( value );
					} );
				}
			} );
		} );
	};
	$.wpcf7UpdateScreenReaderResponse = function( $form, data ) {
		$( '.wpcf7 .screen-reader-response' ).html( '' ).attr( 'role', '' );
		if ( data.message ) {
			var $response = $form.siblings( '.screen-reader-response' ).first();
			$response.append( data.message );
			if ( data.invalids ) {
				var $invalids = $( '<ul></ul>' );
				$.each( data.invalids, function( i, n ) {
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
		}
	};
	$.wpcf7SupportHtml5 = function() {
		var features = {};
		var input = document.createElement( 'input' );
		features.placeholder = 'placeholder' in input;
		var inputTypes = [ 'email', 'url', 'tel', 'number', 'range', 'date' ];
		$.each( inputTypes, function( index, value ) {
			input.setAttribute( 'type', value );
			features[ value ] = input.type !== 'text';
		} );
		return features;
	};
	$( function() {
		_wpcf7.supportHtml5 = $.wpcf7SupportHtml5();
		$( 'div.wpcf7 > form' ).wpcf7InitForm();
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
