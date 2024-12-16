var top_banner = document.querySelector('.wprv-box__head');
var brand_banner = document.querySelector('.background-image__wrapper');
var top_calendar = document.querySelector('.sp_top_wrapper');

if ( top_banner && brand_banner && top_calendar ) {
	brand_banner.style.top = "322px";
}

document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.sp_block_accord .sp_block_accord_title' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.sp_block_accord');
        parentBlock.classList.toggle('open');
    }
} );


/*document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.sp_more_btn' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.sp_more_wrapper');
        [].forEach.call( parentBlock.querySelectorAll( '.sp_more_hidden' ), function( el ) {
            el.classList.remove( 'sp_more_hidden' );
        } );
        event.target.parentNode.remove();

    }
} );
*/
document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.sp_event_top_league_toggler' ) || event.target.matches( '.sp_event_top_league_toggler_open' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.sp_event_top_league');
        [].forEach.call( parentBlock.querySelectorAll( '.sp_event_top_league_links' ), function( el ) {
            el.classList.toggle( 'open' );
        } );

    }
} );

document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.block-btn' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.block-wrapper');
        let dataBlock = event.target.getAttribute('data-block');
        [].forEach.call( parentBlock.querySelectorAll( '.block-content' ), function( el ) {
            if ( dataBlock == 'all' ) {
            	el.classList.add( 'open' );
            } else {
            	el.classList.remove( 'open' );
            	document.getElementById(dataBlock).classList.add( 'open' );
            }
        } );        
        [].forEach.call( parentBlock.querySelectorAll( '.block-btn' ), function( el ) {
            el.classList.remove( 'active' );
            event.target.classList.add( 'active' );

        } );

    }
} );

document.addEventListener( 'click', function( event ) {
    if ( event.target.matches( '.tab-btn' ) ) {
        event.preventDefault();
        let parentBlock = event.target.closest('.tab-wrapper');
        let dataBlock = event.target.getAttribute('data-tab');
        [].forEach.call( parentBlock.querySelectorAll( '.tab-content' ), function( el ) {
            el.classList.remove( 'open' );
            document.getElementById(dataBlock).classList.add( 'open' );
        } );        
        [].forEach.call( parentBlock.querySelectorAll( '.tab-btn' ), function( el ) {
            el.classList.remove( 'active' );
            event.target.classList.add( 'active' );
        } );

    }
} );

function scrollHorizontally(e) {
    e = window.event || e;
    var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
    this.scrollLeft -= (delta * 40); 
    e.preventDefault();
}
if (document.getElementById('sp_filter_top_scroll')) {
    document.getElementById('sp_filter_top_scroll').addEventListener('mousewheel', scrollHorizontally, false);
    document.getElementById('sp_filter_top_scroll').addEventListener('DOMMouseScroll', scrollHorizontally, false);
} else {
    //document.getElementById('sp_filter_top_scroll').attachEvent('onmousewheel', scrollHorizontally);
}


let SPAjax = {
	rest: function( args, callbackResponse ) {
		args.call_action = args.action;
		args.action = 'sp_ajax_on_rest_call';
		this.send( SP_SNS.wp_url + '/?rest_route=/sp_sns/sp_ajax_on_rest_call/', args, callbackResponse );
	},
	post: function( args, callbackResponse ) {
		this.send( SP_SNS.ajax_url, args, callbackResponse );
	},
	send: function( url, args, callbackResponse ) {

		let formData = new FormData();
		formData.append( '_wpnonce', SP_SNS.nonce );

		for ( let key in args ) {
			formData.append( key, args[key] );
		}

		fetch( url, {
			method: 'POST',
			body: formData,
		} ).then( function( response ) {
			response.json().then( function( result ) {
				if ( callbackResponse ) {
					callbackResponse( result );
				}
			} );
		} );

	},
};

let SPSNS = {

	filter_date: '',
	filter_type: '',
	filter_status: '',
	filter_league: '',
	filter_team: '',
	filter_team_in: '',
	filter_team_out: '',
	filter_season: '',
	filter_day: '',
	filter_predict: '',
	filter_offset: '',
	event: new CustomEvent( 'ajax_event', {} ),

	scheduleMainFilter: function( by, e ) {
		this.filter_date_from = document.querySelector( '#sp_filter_main_date_from' ).value;
		this.filter_date_to   = document.querySelector( '#sp_filter_main_date_to' ).value;
		this.filter_type      = document.querySelector( '#sp_filter_main_type' ).value;
		this.filter_league    = document.querySelector( '#sp_filter_main_league' ).value;
		this.filter_status    = document.querySelector( '#sp_filter_main_status' ).value;
		this.filter_offset    = document.querySelector( '#sp_filter_main_offset' ).value;
		if ( document.querySelector( '#sp_filter_main_team' ) ) {
			this.filter_team = document.querySelector( '#sp_filter_main_team' ).value;
		}


		var ms_from   = Date.parse(this.filter_date_from);
		var date_from = new Date(ms_from);
		var ms_to     = Date.parse(this.filter_date_to);
		var date_to   = new Date(ms_to);		
		var options = {
		  month: 'long',
		  day: 'numeric',
		};			
		document.querySelector( '#sp_filter_main_title' ).innerHTML = 'Матчи с ' + date_from.toLocaleString("ru", options) + ' по ' + date_to.toLocaleString("ru", options);

		let now = new Date();
		let dateText = now.getFullYear() + '-' + ((now.getMonth() + 1) < 10 ? "0" : "") + (now.getMonth() + 1) + '-' + (now.getDate() < 10 ? "0" : "") + now.getDate();

		if ( this.filter_status == 'future' ) {
			document.querySelector( '#sp_filter_main_date_from' ).removeAttribute( 'max' );
			document.querySelector( '#sp_filter_main_date_from' ).setAttribute( 'min', dateText );
			document.querySelector( '#sp_filter_main_date_to' ).removeAttribute( 'min' );
			document.querySelector( '#sp_filter_main_date_to' ).removeAttribute( 'max' );				
		}

		if ( this.filter_status == 'publish' ) {
			document.querySelector( '#sp_filter_main_date_from' ).removeAttribute( 'min' );
			document.querySelector( '#sp_filter_main_date_from' ).removeAttribute( 'max' );			
			document.querySelector( '#sp_filter_main_date_to' ).removeAttribute( 'min' );
			document.querySelector( '#sp_filter_main_date_to' ).setAttribute( 'max', dateText );
		}

		if ( this.filter_status == '' ) {
			document.querySelector( '#sp_filter_main_date_from' ).removeAttribute( 'min' );
			document.querySelector( '#sp_filter_main_date_from' ).removeAttribute( 'max' );
			document.querySelector( '#sp_filter_main_date_to' ).removeAttribute( 'min' );
			document.querySelector( '#sp_filter_main_date_to' ).removeAttribute( 'max' );			
		}

		if ( this.filter_date_from > dateText ) {
			document.querySelector( '#sp_filter_main_status_publish' ).disabled = true;
			document.querySelector( '#sp_filter_main_status_future' ).disabled = false;
		}

		if ( this.filter_date_to < dateText ) {
			document.querySelector( '#sp_filter_main_status_publish' ).disabled = false;
			document.querySelector( '#sp_filter_main_status_future' ).disabled = true;
		}

		if ( this.filter_date_to == dateText || this.filter_date_from == dateText  ) {
			document.querySelector( '#sp_filter_main_status_publish' ).disabled = false;
			document.querySelector( '#sp_filter_main_status_future' ).disabled = false;
		}		

		this.send( {
			'action': 'SP_SNS_main_filter',
			'date_from': this.filter_date_from,
			'date_to': this.filter_date_to,
			'league': this.filter_league,
			'type' : this.filter_type,
			'status' : this.filter_status,
			'offset' : this.filter_offset,
			'team' : this.filter_team
		}, function( result ) {
			if ( result.content.length > 10 ) {
				document.querySelector( '#sp_filter_main_content' ).innerHTML = result.content; 
			} else {
				document.querySelector( '#sp_filter_main_content' ).innerHTML = 'В выбранный период события не найдены';
			}
			document.querySelector( '#sp_filter_main_league' ).innerHTML = result.leagues;

		}, '#sp_filter_main' );

	},

	scheduleLeagueFilter: function( by, e ) {

		this.filter_league = document.querySelector( '#sp_filter_league_league' ).value;
		this.filter_season = document.querySelector( '#sp_filter_league_season' ).value;

		if ( by === 'team' ) {
			this.filter_team = e.value;
		} else if ( by === 'day' ) {
			this.filter_day = e.value;
		} else if ( by === 'status' ) {
			this.filter_status = e.value;
		} else if ( by === 'predict' ) {
			this.filter_predict = e.value;			
		} else {
			this.filter_team = '';
			this.filter_day = '';
			this.filter_status = '';
			this.filter_predict = '';
		}

		this.send( {
			'action': 'SP_SNS_league_filter',
			'league': this.filter_league,
			'season': this.filter_season,
			'team' : this.filter_team,
			'day' : this.filter_day,
			'status' : this.filter_status,
			'predict' : this.filter_predict
		}, function( result ) {
			document.querySelector( '#sp_filter_league_content' ).innerHTML = result.content;

		}, '#sp_filter_league' );

	},

	scheduleTeamFilter: function( by, e ) {

		this.filter_team = document.querySelector( '#sp_filter_team_team' ).value;
		this.filter_season = document.querySelector( '#sp_filter_team_season' ).value;
		this.filter_date = document.querySelector( '#sp_filter_team_date' ).value;

		if ( by === 'league' ) {
			this.filter_league = e.value;
		} else if ( by === 'date' ) {
			this.filter_date = e.value;
		} else if ( by === 'status' ) {
			this.filter_status = e.value;
		} else if ( by === 'predict' ) {
			this.filter_predict = e.value;			
		} else {
			this.filter_league = '';
			this.filter_status = '';
			this.filter_predict = '';
		}

		this.send( {
			'action': 'SP_SNS_team_filter',
			'league': this.filter_league,
			'season': this.filter_season,
			'team' : this.filter_team,
			'date' : this.filter_date,
			'status' : this.filter_status,
			'predict' : this.filter_predict
		}, function( result ) {
			document.querySelector( '#sp_filter_team_content' ).innerHTML = result.content;

		}, '#sp_filter_team' );

	},
	scheduleTransferFilter: function( by, e ) {

		this.filter_season = document.querySelector( '#sp_filter_transfer_season' ).value;
		this.filter_league = document.querySelector( '#sp_filter_transfer_league' ).value;

		if ( by === 'team_in' ) {
			this.filter_team_in = e.value;
		} else if ( by === 'team_out' ) {
			this.filter_team_out = e.value;
		} else if ( by === 'status' ) {
			this.filter_status = e.value;
		} else if ( by === 'type' ) {
			this.filter_type = e.value;			
		} else {
			this.filter_team_in = '';
			this.filter_team_out = '';
			this.filter_status = '';
			this.filter_type = '';
		}

		this.send( {
			'action': 'SP_SNS_transfer_filter',
			'league': this.filter_league,
			'season': this.filter_season,
			'team_in' : this.filter_team_in,
			'team_out' : this.filter_team_out,
			'status' : this.filter_status,
			'type' : this.filter_type
		}, function( result ) {
			document.querySelector( '#sp_filter_transfer_content' ).innerHTML = result.content;

		}, '#sp_filter_transfer' );

	},

	scheduleTransferMore: function( e ) {

		this.filter_offset   = e.getAttribute('data-offset');
		this.filter_season   = e.getAttribute('data-season');
		this.filter_league   = e.getAttribute('data-league');
		this.filter_team     = e.getAttribute('data-team');
		this.filter_team_in  = e.getAttribute('data-teamin');
		this.filter_team_out = e.getAttribute('data-teamout');
		this.filter_status   = e.getAttribute('data-status');
		this.filter_type     = e.getAttribute('data-type');
		let offset = parseInt(this.filter_offset);

		this.send( {
			'action': 'SP_SNS_transfer_more',
			'league': this.filter_league,
			'season': this.filter_season,
			'team_in' : this.filter_team_in,
			'team_out' : this.filter_team_out,
			'status' : this.filter_status,
			'type' : this.filter_type,
			'team' : this.filter_team,
			'offset' : this.filter_offset,
		}, function( result ) {
			//document.querySelector( '#sp_filter_transfer_content' ).innerHTML = result.content;
			document.querySelector('#sp_transfer_rows').insertAdjacentHTML('beforeend', result.content);
			e.setAttribute('data-offset', offset + 10);

		}, '#sp_transfer_rows' );

	},

	scheduleTopFilter: function( by, e ) {

		this.filter_status = document.querySelector( '#sp_filter_top_status' ).value;
		this.filter_type = document.querySelector( '#sp_filter_top_type' ).value;
		this.filter_league = document.querySelector( '#sp_filter_top_league' ).value;
		this.filter_date = document.querySelector( '#sp_filter_top_date' ).value;
		this.filter_team = document.querySelector( '#sp_filter_top_team' ).value;

		this.send( {
			'action': 'SP_SNS_top_filter',
			'league': this.filter_league,
			'status': this.filter_status,
			'type' : this.filter_type,
			'date' : this.filter_date,
			'team' : this.filter_team,
		}, function( result ) {
			if ( result.content.length ) {
				document.querySelector( '#sp_filter_top_content' ).innerHTML = result.content; 
			} else {
				document.querySelector( '#sp_filter_top_content' ).innerHTML = 'В выбранный период события не найдены';
			}
		}, '#sp_filter_top_scroll' );

	},

	voteEvent: function( event_id, vote ) {

		this.send( {
			'action': 'SP_SNS_vote_event',
			'event_id': event_id,
			'vote': vote,
		}, function( result ) {
			document.querySelector( '.vote_digit_1' ).innerHTML = result.vote1;
			document.querySelector( '.vote_digit_2' ).innerHTML = result.vote2; 
			document.querySelector( '.vote_digit_3' ).innerHTML = result.vote3;
			document.querySelector( '.vote_line_1' ).style.width = result.vote1;
			document.querySelector( '.vote_line_2' ).style.width = result.vote2; 
			document.querySelector( '.vote_line_3' ).style.width = result.vote3;
			document.querySelector( '.vote_buttons' ).classList.remove('active');
			document.querySelector( '.vote_results' ).classList.add('active');
		}, '#sp_event_header__vote' );

	},		
	startLoading: function( selector ) {
		document.querySelector( selector ).classList.add( 'loading' );
	},
	endLoading: function( selector ) {
		document.querySelector( selector ).classList.remove( 'loading' );
	},
	send: function( data, callback, selector ) {
		let self = this;
		this.startLoading( selector );
		SPAjax.rest( data, function( result ) {
			callback( result );
			self.endLoading( selector );
			document.dispatchEvent( self.event );
		} );
	},
};

if ( document.querySelector( '#sp_filter_top_content' ).innerHTML.length < 50 ) {
	document.querySelector( '#sp_filter_top_content' ).innerHTML = 'В выбранный период события не найдены';
}
console.log( document.querySelector( '#sp_filter_top_content' ).innerHTML.length );
