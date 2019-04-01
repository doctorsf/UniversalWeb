//----------------------------------------------------------------------------------
// Equivalent de la fonction inArray PHP
//----------------------------------------------------------------------------------
function inArray(p_val, array) {
   var l = array.length;
   for(var i = 0; i < l; i++) {
       if(array[i] == p_val) {
           return true;
       }
   }
   return false;
}

//---------------------------------------------------------
//Fonction javascript qui réécrives les fonctions PHP
//sources trouvées sur le site www.phpjs.org
//---------------------------------------------------------

function explode(delimiter, string, limit) {
  //  discuss at: http://phpjs.org/functions/explode/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  //   example 1: explode(' ', 'Kevin van Zonneveld');
  //   returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}

  if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') return null;
  if (delimiter === '' || delimiter === false || delimiter === null) return false;
  if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
    'object') {
    return {
      0: ''
    };
  }
  if (delimiter === true) delimiter = '1';

  // Here we go...
  delimiter += '';
  string += '';

  var s = string.split(delimiter);

  if (typeof limit === 'undefined') return s;

  // Support for limit
  if (limit === 0) limit = 1;

  // Positive limit
  if (limit > 0) {
    if (limit >= s.length) return s;
    return s.slice(0, limit - 1)
      .concat([s.slice(limit - 1)
        .join(delimiter)
      ]);
  }

  // Negative limit
  if (-limit >= s.length) return [];

  s.splice(s.length + limit);
  return s;
}

//---------------------------------------------------------
//Fonction javascript qui réécrives les fonctions PHP
//sources trouvées sur le site www.phpjs.org
//---------------------------------------------------------

function strpos(haystack, needle, offset) {
  //  discuss at: http://phpjs.org/functions/strpos/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Daniel Esteban
  //   example 1: strpos('Kevin van Zonneveld', 'e', 5);
  //   returns 1: 14

  var i = (haystack + '')
    .indexOf(needle, (offset || 0));
  return i === -1 ? false : i;
}

function str_replace(search, replace, subject, count) {
  //  discuss at: http://phpjs.org/functions/str_replace/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Gabriel Paderni
  // improved by: Philip Peterson
  // improved by: Simon Willison (http://simonwillison.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // bugfixed by: Anton Ongson
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Oleg Eremeev
  //    input by: Onno Marsman
  //    input by: Brett Zamir (http://brett-zamir.me)
  //    input by: Oleg Eremeev
  //        note: The count parameter must be passed as a string in order
  //        note: to find a global variable in which the result will be given
  //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
  //   returns 1: 'Kevin.van.Zonneveld'
  //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
  //   returns 2: 'hemmo, mars'
  // bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca)
  //   example 3: str_replace(Array('S','F'),'x','ASDFASDF');
  //   returns 3: 'AxDxAxDx'
  // bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca) Corrected count
  //   example 4: str_replace(['A','D'], ['x','y'] , 'ASDFASDF' , 'cnt');
  //   returns 4: 'xSyFxSyF' // cnt = 0 (incorrect before fix)
  //   returns 4: 'xSyFxSyF' // cnt = 4 (correct after fix)
  
  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  
  if(typeof(search) === 'object' && typeof(replace) === 'string' ) {
    temp = replace; 
    replace = new Array();
    for (i=0; i < search.length; i+=1) { 
      replace[i] = temp; 
    }
    temp = ''; 
    r = [].concat(replace); 
    ra = Object.prototype.toString.call(r) === '[object Array]';
  }
  
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp)
        .split(f[j])
        .join(repl);
      if (count) {
        this.window[count] += ((temp.split(f[j])).length - 1);
      } 
    }
  }
  return sa ? s : s[0];
}

function ucfirst(str) {
  //  discuss at: http://phpjs.org/functions/ucfirst/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  //   example 1: ucfirst('kevin van zonneveld');
  //   returns 1: 'Kevin van zonneveld'

  str += '';
  var f = str.charAt(0)
    .toUpperCase();
  return f + str.substr(1);
}

function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}