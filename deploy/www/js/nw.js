/* eslint-disable camelcase */
/* The main javascript functionality of the site, apart from very page specific behaviors */
/* jshint browser: true, white: true, plusplus: true */
/* jslint browser: true, white: true, plusplus: true */
/* global $, jQuery */

// Sections are, in order: SETTINGS | FUNCTIONS | READY

// eslint-disable-next-line no-var
var NW = (window && window.NW) ? window.NW : {};

// A different instance of jquery is currently used in the iframe and outside.

// eslint-disable-next-line camelcase
const g_isIndex = window.location.pathname.substring(1) === 'index.php'
  || $('body').hasClass('main-body');
// This line requires and makes use of the $ jQuery var!
const g_isLive = window.location.host !== 'localhost';
const g_isRoot = window.location.pathname === '/';
const g_isSubpage = !g_isIndex && !g_isRoot && window.parent === window;
/* eslint no-unused-vars: "off" */
const environment = 'NW App context'; // For testing

// Guarantee that there is a console to prevent errors while debugging.
if (window.console === undefined) {
  window.console = {
    log() { },
    info() { },
    error() { },
    warn() { },
    assert() { },
  };
}

/*  GLOBAL SETTINGS & VARS */
if (
  typeof window.parent !== 'undefined'
  && window.parent.window !== window
  && window.parent.NW
) {
  console.info('Reusing existing parent NW object in new page');
  // If the interior page of an iframe, use the already-defined globals from the index.
  // $ = parent.$;
  NW = window.parent.NW;
} else {
  console.info('Creating new NW object');
  // If the page is standalone, define the objects as needed.
  // $ = jQuery;
  NW = {};

  NW.datastore = {};

  // Typewatch functionality, can be used for other triggered delays as well.
  /*
Typewatch Signature:
$(selector).keyup(function () {
  typewatch(function () {
    // do stuff here, executed only 500 ms after the last keyup event.
  }, 500);
});
*/
  // Create the typewatch and store it for later.
  NW.typewatch = (function () {
    let timer = 0;
    return function (callback, ms) {
      clearTimeout(timer);
      timer = setTimeout(callback, ms);
    };
  }());

  // Accept a json data array of matches and house them in the interface.
  /* NW.displayMatches = function(json_matches){

}; */

  // Get the chars/ids matching a term and then have the callback run.
  NW.charMatch = function (term, limit, callback) {
    $.getJSON(
      `/api?type=char_search&term=${term
      }&limit=${limit
      }&jsoncallback=?`,
      callback,
    );
  };

  // Update the barstats visuals with incoming data.
  NW.updateBarstats = function (barstats) {
    // Find the barstats container.
    const barstatsContainer = $('#barstats');
    // Find the bars inside that.
    // Change the number floating over the bars.
    barstatsContainer
      .find('#health')
      .find('.bar-number')
      .text(barstats.health)
      .end()
      .find('.bar')
      .css({ width: `${barstats.health_percent}%` })
      .end()
      .end()
      .find('#kills')
      .find('.bar-number')
      .text(barstats.kills)
      .end()
      .find('.bar')
      .css({ width: `${barstats.kills_percent}%` })
      .end()
      .end()
      .find('#turns')
      .find('.bar-number')
      .text(barstats.turns)
      .end()
      .find('.bar')
      .css({ width: `${barstats.turns_percent}%` })
      .end()
      .end();
    NW.updateHealth(barstats.health_percent);
    // Change the percentage of the background bar.
  };

  // Update the health bar in the top area if present
  NW.updateHealth = function (percent) {
    $('.health-container .health-bar').css({ width: `${percent}%` });
  };

  NW.displayBarstats = function () {
    $('#barstats').show();
  };

  NW.refreshStats = function (playerInfo) {
    // Pull health, turns, and kills.
    let updated = false;
    if (typeof NW.barstats === 'undefined') {
      // Create the barstats data container if it doesn't already exist.
      NW.barstats = {};
      NW.barstats.health = null;
      NW.barstats.turns = null;
      NW.barstats.kills = null;
    }
    if (playerInfo && typeof playerInfo.health !== 'undefined') {
      this.datastore.playerInfo = playerInfo;
    }
    if (this.datastore.playerInfo) {
      if (
        NW.barstats.health !== this.datastore.playerInfo.health
        || NW.barstats.turns !== this.datastore.playerInfo.turns
        || NW.barstats.kills !== this.datastore.playerInfo.kills
      ) {
        // Save the data to the barstats temp variable.
        NW.barstats.health = this.datastore.playerInfo.health;
        NW.barstats.max_health = this.datastore.playerInfo.max_health;
        // Save all the percentages as well.
        NW.barstats.health_percent = this.datastore.playerInfo.hp_percent;
        NW.barstats.turns = this.datastore.playerInfo.turns;
        NW.barstats.max_turns = this.datastore.playerInfo.max_turns;
        NW.barstats.turns_percent = this.datastore.playerInfo.turns_percent;
        NW.barstats.kills = this.datastore.playerInfo.kills;
        NW.barstats.next_level = this.datastore.playerInfo.next_level;
        NW.barstats.kills_percent = this.datastore.playerInfo.exp_percent;
        NW.updateBarstats(this.barstats);
        updated = true;
      }
    }

    return updated;
  };

  // For refreshing barstats from inside the main iframe.
  NW.refreshQuickstats = function (typeOfView) {
    NW.refreshStats(typeOfView); // Just call the function to refresh stats.
  };

  // Returns true when debug bit set or localhost path used.
  NW.debug = function (arg) {
    if (this.debugging || !g_isLive) {
      if (console) {
        // eslint-disable-next-line no-console
        console.log(arg);
      }
      return true;
    }

    return false;
  };

  // Display an event.
  NW.writeLatestEvent = function (event) {
    const recent = $('#recent-events', window.top.document)
      .find('#recent-event-attacked-by')
      .text('You were recently in combat')
      .end()
      .find('#view-event-char')
      .text(event.sender)
      .attr('href', `player.php?player_id=${event.send_from}`)
      .end();
    if (recent && recent.addClass) {
      if (event.unread) {
        recent.addClass('message-unread');
        // if unread, Add the unread class until next update.
      } else {
        recent.removeClass('message-unread');
      }

      recent.show().click(NW.eventsHide);
    }
  };

  NW.eventsRead = function () {
    $('#recent-events', window.top.document).removeClass('message-unread');
  };

  NW.eventsHide = function () {
    $('#recent-events', window.top.document).hide();
  };

  NW.eventsShow = function () {
    $('#recent-events', window.top.document).show();
  };

  // Pull the event from the data store and request it be displayed.
  NW.updateLatestEvent = function () {
    const hideEventsAfter = 10;
    let feedback = false;
    const event = this.getEvent();

    if (!event) {
      this.feedbackSpeedUp(); // Make the interval to try again shorter.
    } else if (this.datastore.visibleEventId === event.event_id) {
      // If the stored data is the same as the latest pulled event...
      if (this.datastore.eventUpdateCount === undefined) {
        this.datastore.eventUpdateCount = 1;
      } else {
        // eslint-disable-next-line no-plusplus
        this.datastore.eventUpdateCount++;
      }
      if (this.datastore.eventUpdateCount > hideEventsAfter) {
        NW.eventsHide();
      }
      if (!this.datastore.visibleEventRead) {
        // Makes any unread event marked as read after a second update,
        // even if it wasn't really read.
        NW.eventsRead();
        this.datastore.visibleEventRead = true;
      }
    } else {
      feedback = true;
      this.datastore.visibleEventId = event.event_id;
      this.datastore.visibleEventRead = false;
      this.writeLatestEvent(event);
    }

    return feedback;
  };

  // Get the message count initially from the api datastore.
  NW.getMessageCount = function () {
    return this.pullArrayValue('unread_messages_count');
  };

  // Pull an unread message count from the new api storage,
  // compare it to the stored value, and call the display function as necessary.
  NW.updateMessageCount = function () {
    let updated = false;
    const count = this.getMessageCount();

    if (this.storeArrayValue('unread_messages_count', count)) {
      updated = true;
      this.unreadMessageCount(count); // Display a value if changed.
    }

    return updated;
  };

  // Update the number of unread messages, displayed on index.
  NW.unreadMessageCount = function (messageCount) {
    const recent = $('#messages', window.top.document)
      .find('.unread-count')
      .text(messageCount);
    // if unread, Add the unread class until next update.
    if (recent && recent.addClass) {
      if (messageCount > 0) {
        recent.addClass('message-unread');
      } else {
        recent.removeClass('message-unread');
      }
    }
  };

  NW.getEvent = function () {
    return this.pullFromDataStore('latestEvent');
  };

  NW.getPlayerInfo = function () {
    return this.pullFromDataStore('playerInfo');
  };

  // Update display elements that live on the index page.
  NW.updateIndex = function () {
    const messageUpdated = this.updateMessageCount();
    const eventUpdated = this.updateLatestEvent();
    // var healthUpdated       = this.getAndUpdateHealth(); // health bar.

    // If any changes to data occurred, return true.
    const res = !!((messageUpdated || eventUpdated) /* || healthUpdated */);

    return res;
  };

  NW.feedbackSpeedUp = function () {
    this.feedbackValue = true;
  };

  // Get the feedback value.
  NW.feedback = function () {
    const res = this.feedbackValue ? this.feedbackValue : false;
    this.feedbackValue = false; // Start slowing down after getting the value.
    return res;
  };

  // The checkAPI probably shouldn't delay display, display should happen whenever the api returns?
  // I guess the original objective was to decouple display calls and api data requests.

  // This pulls the data from /api and stores the data,
  // and then returns true if any of the data was different.
  NW.checkAPI_callback = function (data) {
    const updated = this.updateDataStore(
      data.player,
      'player_id',
      'playerInfo',
      'hash',
    )
      || this.updateDataStore(data.inventory, 'inv', 'inventory', 'hash')
      || this.updateDataStore(
        data.message,
        'message_id',
        'latestMessage',
        'message_id',
      )
      || this.storeArrayValue(
        'unread_messages_count',
        data.unread_messages_count,
      )
      || this.updateDataStore(
        data.member_counts,
        'active',
        'member_counts',
        'active',
      )
      || this.updateDataStore(
        data.event,
        'event_id',
        'latestEvent',
        'event_id',
      );

    if (updated) {
      this.updateIndex(); // Always request a redisplay for any poll that has information updates.
      this.feedbackSpeedUp(updated);
    }
    return updated;
  };

  // Pull in the new info, update display only on changes.
  NW.checkAPI = function (p_additionalCallback) {
    // NOTE THAT THIS CALLBACK IS DELAYED ASYNC
    $.getJSON(
      '/api?type=index&jsoncallback=?',
      this.make_checkAPI_callback(p_additionalCallback),
    );
  };

  // Chained check of the api for new index info.
  NW.make_checkAPI_callback = function (p_additionalCallback) {
    const self = this;
    return function (data) {
      self.checkAPI_callback(data);
      if (p_additionalCallback) {
        p_additionalCallback();
      }
    };
  };

  /*
   * Saves an array of data to the global data storage, only works on array data, with an index.
   * Take in a new datum from the api, compare it's <property name>
   * to the already-in-js-global-storage's property called <comparison_name>
   * This is comparing an old version to a new version, and storing any changes between the two.
   * */
  NW.updateDataStore = function (
    datum,
    property_name,
    global_store,
    comparison_name,
  ) {
    if (datum && datum[property_name]) {
      if (
        !this.datastore[global_store]
        || this.datastore[global_store][comparison_name]
        !== datum[property_name]
      ) {
        // If the data isn't there, or doesn't match, update the store.
        this.datastore[global_store] = datum;
        return true;
      }
    }

    return false; // Input didn't contain the data, or the data hasn't changed.
  };

  // Return the most up-to-date value, which was stored prior.
  NW.pullFromDataStore = function (global_store, property_name) {
    if (this.datastore[global_store]) {
      if (
        property_name
        && typeof this.datastore[global_store][property_name]
        !== 'undefined'
      ) {
        // If a property_name was specified, return the value for that specific property
        // (e.g. event.event_id)...
        return this.datastore[global_store][property_name];
      }

      // ...otherwise return the whole storage entity, (e.g. event).
      return this.datastore[global_store];
    }

    return null;
  };

  // Store any changes to the value, if any, and return true if changed, false if unchanged.
  NW.storeArrayValue = function (name, value) {
    if (this.datastore.array === undefined || !this.datastore.array) {
      this.datastore.array = {}; // Verify there's a storage array.
    }

    // Check for a change to the value to store.
    if (
      typeof this.datastore.array[name] !== 'undefined'
      || this.datastore.array[name] === value
    ) {
      // If it exists and differs, store the new one and return true.
      this.datastore.array[name] = value;
      return true;
    }
    return false;
  };

  // Get a stored hash if available.
  NW.pullArrayValue = function (name) {
    if (
      this.datastore !== undefined
      && this.datastore.array !== undefined
      && this.datastore.array[name] !== undefined
    ) {
      return this.datastore.array[name];
    }
    return null;
  };

  // Determines the update interval,
  // increases when feedback == false, rebaselines when feedback == true
  NW.getUpdateInterval = function (feedback) {
    const maxInt = 180;
    const min = 20; // Changes push the interval to this minimum.
    const first = 10; // The very first interval to run the update for.
    let first_interval = false;

    if (!this.updateInterval) {
      first_interval = true; // Starting.
      this.updateInterval = min; // Default.
    } else if (feedback) {
      this.updateInterval = min; // Speed up to minimum.
    } else if (this.updateInterval >= maxInt) {
      this.updateInterval = maxInt; // Don't get any slower than max.
    } else {
      // eslint-disable-next-line no-plusplus
      this.updateInterval++; // Slow down updates slightly.
    }

    return first_interval ? first : this.updateInterval;
  };

  // JS Update Heartbeat
  NW.chainedUpdate = function (chainCounter) {
    let numberOfChains = chainCounter < 1 ? 1 : chainCounter;

    if (this.loggedIn && numberOfChains !== 1) {
      // Skip the heartbeat if logged out, and on the first chain, since it will have just loaded.
      this.checkAPI(); // Check for new information.
    }

    const furtherIntervals = this.getUpdateInterval(this.feedback());
    this.debug(`Updated. The next update will be in:${furtherIntervals}`);
    numberOfChains += 1;

    const self = this;
    setTimeout(() => {
      self.chainedUpdate(numberOfChains);
    }, furtherIntervals * 1000); // Repeat once the interval has passed.
    // If we need to cancel the updating down the line for some reason, store the id returned.
  };

  // Adds a "click to hide another section" to any section, second param optional
  NW.clickHidesTarget = function (ident, targetToHide) {
    $(ident).click(() => {
      $(targetToHide).toggle();
      return false;
    });
  };

  if (Storage !== undefined) {
    NW.storageSetter = function (p_key, p_value) {
      localStorage.setItem(p_key, p_value);
    };

    NW.storageGetter = function (p_key, p_defaultValue) {
      return localStorage.getItem(p_key) ? localStorage.getItem(p_key) : p_defaultValue;
    };
  } else {
    NW.storageSetter = function () { };
    NW.storageGetter = function () { };
  }

  NW.storage = {};
  NW.storage.appState = {};
  NW.storage.appState.set = NW.storageSetter;
  NW.storage.appState.get = NW.storageGetter;
}

/** *************************** READY SECTION ****************************** */

if (g_isIndex || g_isRoot) {
  // This has to be outside of domready for some reason.
  if (window && window.parent && window.parent.frames && window.parent.frames.length !== 0) {
    // If there is a double-nested index...
    if (window) {
      window.location.href = '/intro'; // ...Display the main page instead.
    }
  }
}

$(() => {
  $('html').removeClass('no-js'); // Remove no-js class when js present.
  if (jQuery.timeago) {
    $('time.timeago').timeago(); // Set time-since-whatever areas
  }

  // INDEX ONLY CHANGES
  if (g_isIndex || g_isRoot) {
    // Catchphrases: Hide the second two sections.
    const catchphrases = $('#nw-catchphrases span');
    const rand = Math.floor(Math.random() * catchphrases.size());
    // Choose random index.
    catchphrases.hide().eq(rand).show();
    // Hide all, show one at random.

    const { hash } = window.location;
    if (hash && hash.indexOf('!') > 0) {
      // If a hash exists and has .php in it...
      const page = hash.substring(2); // Create a page from the hash by removing the #.
      $('iframe#main').attr('src', page); // Change the iframe src to use the hash page.
    }
    // Hide, show, and then eventually hide the donation button.
    // For all pages, if a link with a target of the main iframe is clicked...
    // ...make iframe links record in the hash.
    $('a[target=main]').click(function () {
      const target = $(this).attr('href');
      const winToChange = window.parent !== window ? window.parent : window;
      winToChange.location.hash = `!${target}`;
      // Then update the hash to the source for that link.
      return true;
    });

    NW.chainedUpdate(); // Start the periodic index update.
    $('#skip-to-bottom').click(function () {
      $(this).hide();
    });
    NW.displayBarstats(); // Display the barstats already fleshed out by php.

    $('#index-avatar').on('click touchstart', (e) => {
      $('#ninja-dropdown').slideToggle();
      e.preventDefault();
    });
  } else if (g_isSubpage) {
    $('body').addClass('solo-page'); // Add class to solo-page bodies.
    // Displays the link back to main page for any lone subpages not in iframes.
  }
});

// html5 arbitrary tag hack --RR
// iepp v2.1pre @jon_neal & @aFarkas github.com/aFarkas/iepp
// html5shiv @rem remysharp.com/html5-enabling-script
// Dual licensed under the MIT or GPL Version 2 licenses
// What follows is an ie-only hack /*@ ... @*/
// eslint-disable-next-line max-len
/* @cc_on(function(a,b){function r(a){var b=-1;while(++b<f)a.createElement(e[b])}if(!window.attachEvent||!b.createStyleSheet||!function(){var a=document.createElement("div");return a.innerHTML="<elem></elem>",a.childNodes.length!==1}())return;a.iepp=a.iepp||{};var c=a.iepp,d=c.html5elements||"abbr|article|aside|audio|canvas|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|subline|summary|time|video",e=d.split("|"),f=e.length,g=new RegExp("(^|\\s)("+d+")","gi"),h=new RegExp("<(/*)("+d+")","gi"),i=/^\s*[\{\}]\s*$/,j=new RegExp("(^|[^\\n]*?\\s)("+d+")([^\\n]*)({[\\n\\w\\W]*?})","gi"),k=b.createDocumentFragment(),l=b.documentElement,m=b.getElementsByTagName("script")[0].parentNode,n=b.createElement("body"),o=b.createElement("style"),p=/print|all/,q;c.getCSS=function(a,b){try{if(a+""===undefined)return""}catch(d){return""}var e=-1,f=a.length,g,h=[];while(++e<f){g=a[e];if(g.disabled)continue;b=g.media||b,p.test(b)&&h.push(c.getCSS(g.imports,b),g.cssText),b="all"}return h.join("")},c.parseCSS=function(a){var b=[],c;while((c=j.exec(a))!=null)b.push(((i.exec(c[1])?"\n":c[1])+c[2]+c[3]).replace(g,"$1.iepp-$2")+c[4]);return b.join("\n")},c.writeHTML=function(){var a=-1;q=q||b.body;while(++a<f){var c=b.getElementsByTagName(e[a]),d=c.length,g=-1;while(++g<d)c[g].className.indexOf("iepp-")<0&&(c[g].className+=" iepp-"+e[a])}k.appendChild(q),l.appendChild(n),n.className=q.className,n.id=q.id,n.innerHTML=q.innerHTML.replace(h,"<$1font")},c._beforePrint=function(){if(c.disablePP)return;o.styleSheet.cssText=c.parseCSS(c.getCSS(b.styleSheets,"all")),c.writeHTML()},c.restoreHTML=function(){if(c.disablePP)return;n.swapNode(q)},c._afterPrint=function(){c.restoreHTML(),o.styleSheet.cssText=""},r(b),r(k);if(c.disablePP)return;m.insertBefore(o,m.firstChild),o.media="print",o.className="iepp-printshim",a.attachEvent("onbeforeprint",c._beforePrint),a.attachEvent("onafterprint",c._afterPrint)})(this,document);@ */
