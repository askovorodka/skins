/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(['jquery'], factory);
	} else if (typeof exports === 'object') {
		// CommonJS
		factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function encode(s) {
		return config.raw ? s : encodeURIComponent(s);
	}

	function decode(s) {
		return config.raw ? s : decodeURIComponent(s);
	}

	function stringifyCookieValue(value) {
		return encode(config.json ? JSON.stringify(value) : String(value));
	}

	function parseCookieValue(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape...
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}

		try {
			// Replace server-side written pluses with spaces.
			// If we can't decode the cookie, ignore it, it's unusable.
			// If we can't parse the cookie, ignore it, it's unusable.
			s = decodeURIComponent(s.replace(pluses, ' '));
			return config.json ? JSON.parse(s) : s;
		} catch(e) {}
	}

	function read(s, converter) {
		var value = config.raw ? s : parseCookieValue(s);
		return $.isFunction(converter) ? converter(value) : value;
	}

	var config = $.cookie = function (key, value, options) {

		// Write

		if (value !== undefined && !$.isFunction(value)) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setTime(+t + days * 864e+5);
			}

			return (document.cookie = [
				encode(key), '=', stringifyCookieValue(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// Read

		var result = key ? undefined : {};

		// To prevent the for loop in the first place assign an empty array
		// in case there are no cookies at all. Also prevents odd result when
		// calling $.cookie().
		var cookies = document.cookie ? document.cookie.split('; ') : [];

		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = parts.join('=');

			if (key && key === name) {
				// If second argument (value) is a function it's a converter...
				result = read(cookie, value);
				break;
			}

			// Prevent storing a cookie that we couldn't decode.
			if (!key && (cookie = read(cookie)) !== undefined) {
				result[name] = cookie;
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) === undefined) {
			return false;
		}

		// Must not alter options, thus extending a fresh object...
		$.cookie(key, '', $.extend({}, options, { expires: -1 }));
		return !$.cookie(key);
	};

}));
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiIiwic291cmNlcyI6WyJqcXVlcnkuY29va2llLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qIVxuICogalF1ZXJ5IENvb2tpZSBQbHVnaW4gdjEuNC4xXG4gKiBodHRwczovL2dpdGh1Yi5jb20vY2FyaGFydGwvanF1ZXJ5LWNvb2tpZVxuICpcbiAqIENvcHlyaWdodCAyMDEzIEtsYXVzIEhhcnRsXG4gKiBSZWxlYXNlZCB1bmRlciB0aGUgTUlUIGxpY2Vuc2VcbiAqL1xuKGZ1bmN0aW9uIChmYWN0b3J5KSB7XG5cdGlmICh0eXBlb2YgZGVmaW5lID09PSAnZnVuY3Rpb24nICYmIGRlZmluZS5hbWQpIHtcblx0XHQvLyBBTURcblx0XHRkZWZpbmUoWydqcXVlcnknXSwgZmFjdG9yeSk7XG5cdH0gZWxzZSBpZiAodHlwZW9mIGV4cG9ydHMgPT09ICdvYmplY3QnKSB7XG5cdFx0Ly8gQ29tbW9uSlNcblx0XHRmYWN0b3J5KHJlcXVpcmUoJ2pxdWVyeScpKTtcblx0fSBlbHNlIHtcblx0XHQvLyBCcm93c2VyIGdsb2JhbHNcblx0XHRmYWN0b3J5KGpRdWVyeSk7XG5cdH1cbn0oZnVuY3Rpb24gKCQpIHtcblxuXHR2YXIgcGx1c2VzID0gL1xcKy9nO1xuXG5cdGZ1bmN0aW9uIGVuY29kZShzKSB7XG5cdFx0cmV0dXJuIGNvbmZpZy5yYXcgPyBzIDogZW5jb2RlVVJJQ29tcG9uZW50KHMpO1xuXHR9XG5cblx0ZnVuY3Rpb24gZGVjb2RlKHMpIHtcblx0XHRyZXR1cm4gY29uZmlnLnJhdyA/IHMgOiBkZWNvZGVVUklDb21wb25lbnQocyk7XG5cdH1cblxuXHRmdW5jdGlvbiBzdHJpbmdpZnlDb29raWVWYWx1ZSh2YWx1ZSkge1xuXHRcdHJldHVybiBlbmNvZGUoY29uZmlnLmpzb24gPyBKU09OLnN0cmluZ2lmeSh2YWx1ZSkgOiBTdHJpbmcodmFsdWUpKTtcblx0fVxuXG5cdGZ1bmN0aW9uIHBhcnNlQ29va2llVmFsdWUocykge1xuXHRcdGlmIChzLmluZGV4T2YoJ1wiJykgPT09IDApIHtcblx0XHRcdC8vIFRoaXMgaXMgYSBxdW90ZWQgY29va2llIGFzIGFjY29yZGluZyB0byBSRkMyMDY4LCB1bmVzY2FwZS4uLlxuXHRcdFx0cyA9IHMuc2xpY2UoMSwgLTEpLnJlcGxhY2UoL1xcXFxcIi9nLCAnXCInKS5yZXBsYWNlKC9cXFxcXFxcXC9nLCAnXFxcXCcpO1xuXHRcdH1cblxuXHRcdHRyeSB7XG5cdFx0XHQvLyBSZXBsYWNlIHNlcnZlci1zaWRlIHdyaXR0ZW4gcGx1c2VzIHdpdGggc3BhY2VzLlxuXHRcdFx0Ly8gSWYgd2UgY2FuJ3QgZGVjb2RlIHRoZSBjb29raWUsIGlnbm9yZSBpdCwgaXQncyB1bnVzYWJsZS5cblx0XHRcdC8vIElmIHdlIGNhbid0IHBhcnNlIHRoZSBjb29raWUsIGlnbm9yZSBpdCwgaXQncyB1bnVzYWJsZS5cblx0XHRcdHMgPSBkZWNvZGVVUklDb21wb25lbnQocy5yZXBsYWNlKHBsdXNlcywgJyAnKSk7XG5cdFx0XHRyZXR1cm4gY29uZmlnLmpzb24gPyBKU09OLnBhcnNlKHMpIDogcztcblx0XHR9IGNhdGNoKGUpIHt9XG5cdH1cblxuXHRmdW5jdGlvbiByZWFkKHMsIGNvbnZlcnRlcikge1xuXHRcdHZhciB2YWx1ZSA9IGNvbmZpZy5yYXcgPyBzIDogcGFyc2VDb29raWVWYWx1ZShzKTtcblx0XHRyZXR1cm4gJC5pc0Z1bmN0aW9uKGNvbnZlcnRlcikgPyBjb252ZXJ0ZXIodmFsdWUpIDogdmFsdWU7XG5cdH1cblxuXHR2YXIgY29uZmlnID0gJC5jb29raWUgPSBmdW5jdGlvbiAoa2V5LCB2YWx1ZSwgb3B0aW9ucykge1xuXG5cdFx0Ly8gV3JpdGVcblxuXHRcdGlmICh2YWx1ZSAhPT0gdW5kZWZpbmVkICYmICEkLmlzRnVuY3Rpb24odmFsdWUpKSB7XG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQoe30sIGNvbmZpZy5kZWZhdWx0cywgb3B0aW9ucyk7XG5cblx0XHRcdGlmICh0eXBlb2Ygb3B0aW9ucy5leHBpcmVzID09PSAnbnVtYmVyJykge1xuXHRcdFx0XHR2YXIgZGF5cyA9IG9wdGlvbnMuZXhwaXJlcywgdCA9IG9wdGlvbnMuZXhwaXJlcyA9IG5ldyBEYXRlKCk7XG5cdFx0XHRcdHQuc2V0VGltZSgrdCArIGRheXMgKiA4NjRlKzUpO1xuXHRcdFx0fVxuXG5cdFx0XHRyZXR1cm4gKGRvY3VtZW50LmNvb2tpZSA9IFtcblx0XHRcdFx0ZW5jb2RlKGtleSksICc9Jywgc3RyaW5naWZ5Q29va2llVmFsdWUodmFsdWUpLFxuXHRcdFx0XHRvcHRpb25zLmV4cGlyZXMgPyAnOyBleHBpcmVzPScgKyBvcHRpb25zLmV4cGlyZXMudG9VVENTdHJpbmcoKSA6ICcnLCAvLyB1c2UgZXhwaXJlcyBhdHRyaWJ1dGUsIG1heC1hZ2UgaXMgbm90IHN1cHBvcnRlZCBieSBJRVxuXHRcdFx0XHRvcHRpb25zLnBhdGggICAgPyAnOyBwYXRoPScgKyBvcHRpb25zLnBhdGggOiAnJyxcblx0XHRcdFx0b3B0aW9ucy5kb21haW4gID8gJzsgZG9tYWluPScgKyBvcHRpb25zLmRvbWFpbiA6ICcnLFxuXHRcdFx0XHRvcHRpb25zLnNlY3VyZSAgPyAnOyBzZWN1cmUnIDogJydcblx0XHRcdF0uam9pbignJykpO1xuXHRcdH1cblxuXHRcdC8vIFJlYWRcblxuXHRcdHZhciByZXN1bHQgPSBrZXkgPyB1bmRlZmluZWQgOiB7fTtcblxuXHRcdC8vIFRvIHByZXZlbnQgdGhlIGZvciBsb29wIGluIHRoZSBmaXJzdCBwbGFjZSBhc3NpZ24gYW4gZW1wdHkgYXJyYXlcblx0XHQvLyBpbiBjYXNlIHRoZXJlIGFyZSBubyBjb29raWVzIGF0IGFsbC4gQWxzbyBwcmV2ZW50cyBvZGQgcmVzdWx0IHdoZW5cblx0XHQvLyBjYWxsaW5nICQuY29va2llKCkuXG5cdFx0dmFyIGNvb2tpZXMgPSBkb2N1bWVudC5jb29raWUgPyBkb2N1bWVudC5jb29raWUuc3BsaXQoJzsgJykgOiBbXTtcblxuXHRcdGZvciAodmFyIGkgPSAwLCBsID0gY29va2llcy5sZW5ndGg7IGkgPCBsOyBpKyspIHtcblx0XHRcdHZhciBwYXJ0cyA9IGNvb2tpZXNbaV0uc3BsaXQoJz0nKTtcblx0XHRcdHZhciBuYW1lID0gZGVjb2RlKHBhcnRzLnNoaWZ0KCkpO1xuXHRcdFx0dmFyIGNvb2tpZSA9IHBhcnRzLmpvaW4oJz0nKTtcblxuXHRcdFx0aWYgKGtleSAmJiBrZXkgPT09IG5hbWUpIHtcblx0XHRcdFx0Ly8gSWYgc2Vjb25kIGFyZ3VtZW50ICh2YWx1ZSkgaXMgYSBmdW5jdGlvbiBpdCdzIGEgY29udmVydGVyLi4uXG5cdFx0XHRcdHJlc3VsdCA9IHJlYWQoY29va2llLCB2YWx1ZSk7XG5cdFx0XHRcdGJyZWFrO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyBQcmV2ZW50IHN0b3JpbmcgYSBjb29raWUgdGhhdCB3ZSBjb3VsZG4ndCBkZWNvZGUuXG5cdFx0XHRpZiAoIWtleSAmJiAoY29va2llID0gcmVhZChjb29raWUpKSAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdHJlc3VsdFtuYW1lXSA9IGNvb2tpZTtcblx0XHRcdH1cblx0XHR9XG5cblx0XHRyZXR1cm4gcmVzdWx0O1xuXHR9O1xuXG5cdGNvbmZpZy5kZWZhdWx0cyA9IHt9O1xuXG5cdCQucmVtb3ZlQ29va2llID0gZnVuY3Rpb24gKGtleSwgb3B0aW9ucykge1xuXHRcdGlmICgkLmNvb2tpZShrZXkpID09PSB1bmRlZmluZWQpIHtcblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9XG5cblx0XHQvLyBNdXN0IG5vdCBhbHRlciBvcHRpb25zLCB0aHVzIGV4dGVuZGluZyBhIGZyZXNoIG9iamVjdC4uLlxuXHRcdCQuY29va2llKGtleSwgJycsICQuZXh0ZW5kKHt9LCBvcHRpb25zLCB7IGV4cGlyZXM6IC0xIH0pKTtcblx0XHRyZXR1cm4gISQuY29va2llKGtleSk7XG5cdH07XG5cbn0pKTsiXSwiZmlsZSI6ImpxdWVyeS5jb29raWUuanMifQ==
