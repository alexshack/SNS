var filterItems = document.querySelectorAll( '.loaders-check' );
for ( var i = 0; i < filterItems.length; i += 1 ) {
    filterItems[i].addEventListener( 'change', function (e) {
        returnFilter();
    } );
}
let button_loader = document.getElementById("loader-start");

function returnFilter() {
    var url = new URL(page_loader);
    url.searchParams.set('step', '1');
    var s_leagues = [];
    var s_seasons = [];
    var s_teams = [];
    var s_date_from = [];
    var s_date_to = [];
    var load_titles = [];
    var load_stages = [];
    console.log(url);

    var leagueItems = document.querySelectorAll( '.loaders-league:checked' );
    for ( var i = 0; i < leagueItems.length; i += 1 ) {
        s_leagues.push(leagueItems[i].value);
    }

    var seasonItems = document.querySelectorAll( '.loaders-season:checked' );
    for ( var i = 0; i < seasonItems.length; i += 1 ) {
        s_seasons.push(seasonItems[i].value);
    }

    var teamItems = document.querySelectorAll( '.loaders-team:checked' );
    for ( var i = 0; i < teamItems.length; i += 1 ) {
        s_teams.push(teamItems[i].value);
    }

    if ( document.getElementById('date_from').value ) {
        s_date_from.push(document.getElementById('date_from').value);
    }

    if ( document.getElementById('date_to').value ) {
        s_date_to.push(document.getElementById('date_to').value);
    }    

    if ( document.getElementById('load_titles').checked ) {
        load_titles.push('yes');
    } 

    if ( document.getElementById('load_stages').checked ) {
        load_stages.push('yes');
    } 

    if (s_leagues.length > 0) {
        url.searchParams.set('leagues', s_leagues.toString());
    }
    if (s_seasons.length > 0) {
        url.searchParams.set('seasons', s_seasons.toString());     
    }
    if (s_teams.length > 0) {
        url.searchParams.set('teams', s_teams.toString());     
    }  
    if (s_date_from.length > 0) {
        url.searchParams.set('date_from', s_date_from.toString());     
    }
    if (s_date_to.length > 0) {
        url.searchParams.set('date_to', s_date_to.toString());     
    }         
 
    if (load_titles.length > 0) {
        url.searchParams.set('load_titles', load_titles.toString());     
    }

    if (load_stages.length > 0) {
        url.searchParams.set('load_stages', load_stages.toString());     
    }

    button_loader.href = url;

    //window.location.href = url;
}

returnFilter();