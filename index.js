var historyDate = null;
var historyMin = new Date('2021-01-17');
var historyMax = null;
var historySelector = 'svg-today';
var historyOneDay = [];

function setInfo(servers, selectorStatus, selectorTime, selectorBody, urls) {
	var s;
	var status = true;
	var responseTime = 0;

	for (s = 0; s < servers.servers.length; ++s) {
		var server = servers.servers[s];

		if ((urls.length > 0) && (urls.indexOf(server.url) === -1)) {
			continue;
		}

		if (server.status !== 'on') {
			status = false;
		}
		responseTime = responseTime > parseFloat(server.responseTime) ? responseTime : parseFloat(server.responseTime);
	}

	responseTimeString = parseInt(responseTime * 100) / 100 + '';
	responseTimeString = responseTimeString.replace('.', ',');

	$(selectorTime).html((urls.length === 0 ? 'Reaktionszeit ' : '') + responseTimeString + ' Sekunden');
	$(selectorBody).removeClass('bg-success').removeClass('bg-warning').removeClass('bg-danger').removeClass('text-dark').removeClass('text-light');
	$(selectorBody + ' .small').removeClass('text-dark').removeClass('text-light');
	if (!status) {
		$(selectorStatus).html('NEIN');
		$(selectorTime).html('&nbsp;');
		$(selectorBody).addClass('bg-danger').addClass('text-light');
		$(selectorBody + ' .small').addClass('text-light');
	} else if (responseTime >= 10) {
		$(selectorStatus).html('NEIN');
		$(selectorBody).addClass('bg-danger').addClass('text-light');
		$(selectorBody + ' .small').addClass('text-light');
	} else if (responseTime >= 2.5) {
		$(selectorStatus).html('Ja, aber ganz schön langsam');
		$(selectorBody).addClass('bg-warning').addClass('text-dark');
		$(selectorBody + ' .small').addClass('text-dark');
	} else {
		$(selectorStatus).html('JA');
		$(selectorBody).addClass('bg-success').addClass('text-light');
		$(selectorBody + ' .small').addClass('text-light');
	}
}

function processServersJSON(servers) {
	if (servers.servers) {
		setInfo(servers, '#site-status', '#site-response', '.card-body-main', []);
		setInfo(servers, '#site-status-section1', '#site-response-section1', '.card-body-section1',
			['/start/', '/moodle/', '/moodle/login/index.php', '/v1/', '/v1/my/', '/v1und2/', '/v1und2/my/']);
		setInfo(servers, '#site-status-section2', '#site-response-section2', '.card-body-section2',
			['/start/', '/moodle/', '/moodle/login/index.php', '/v2/', '/v2/my/']);
		setInfo(servers, '#site-status-section3', '#site-response-section3', '.card-body-section3',
			['/start/', '/moodle/', '/moodle/login/index.php', '/v3/', '/v3/my/', '/v3und4/', '/v3und4/my/']);
		setInfo(servers, '#site-status-section4', '#site-response-section4', '.card-body-section4',
			['/start/', '/moodle/', '/moodle/login/index.php', '/v4/', '/v4/my/']);
		setInfo(servers, '#site-status-section5', '#site-response-section5', '.card-body-section5',
			['/start/', '/moodle/', '/moodle/login/index.php', '/osz/', '/osz/my/']);
	} else {
		$( "#site-status" ).html('Keine Ahnung');
		$( "#site-response" ).html('&nbsp;');
	}
}

function paintSVG() {
	var svgns = 'http://www.w3.org/2000/svg';
	var svg = document.getElementById(historySelector);
	var urls = [];
	var urlVal = $('#selectFilter').val();

	if (urlVal !== '') {
		urls.push(urlVal);
	}

	while (svg.firstChild) {
		svg.removeChild(svg.lastChild);
	}


	var rect = document.createElementNS(svgns, 'rect');
	rect.setAttributeNS(null, 'width', 288);
	rect.setAttributeNS(null, 'height', 100);
	rect.setAttributeNS(null, 'fill', '#212529');
	svg.appendChild(rect);

	var line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 1);
	line.setAttributeNS(null, 'x2', 288);
	line.setAttributeNS(null, 'y1', 1);
	line.setAttributeNS(null, 'y2', 1);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 1);
	line.setAttributeNS(null, 'x2', 288);
	line.setAttributeNS(null, 'y1', 25);
	line.setAttributeNS(null, 'y2', 25);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 1);
	line.setAttributeNS(null, 'x2', 288);
	line.setAttributeNS(null, 'y1', 50);
	line.setAttributeNS(null, 'y2', 50);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 1);
	line.setAttributeNS(null, 'x2', 288);
	line.setAttributeNS(null, 'y1', 75);
	line.setAttributeNS(null, 'y2', 75);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 1);
	line.setAttributeNS(null, 'x2', 288);
	line.setAttributeNS(null, 'y1', 99);
	line.setAttributeNS(null, 'y2', 99);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 1);
	line.setAttributeNS(null, 'x2', 1);
	line.setAttributeNS(null, 'y1', 1);
	line.setAttributeNS(null, 'y2', 99);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 96);
	line.setAttributeNS(null, 'x2', 96);
	line.setAttributeNS(null, 'y1', 1);
	line.setAttributeNS(null, 'y2', 99);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 192);
	line.setAttributeNS(null, 'x2', 192);
	line.setAttributeNS(null, 'y1', 1);
	line.setAttributeNS(null, 'y2', 99);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	line = document.createElementNS(svgns, 'line');
	line.setAttributeNS(null, 'x1', 287);
	line.setAttributeNS(null, 'x2', 287);
	line.setAttributeNS(null, 'y1', 1);
	line.setAttributeNS(null, 'y2', 99);
	line.setAttributeNS(null, 'stroke', '#888');
	svg.appendChild(line);

	var latencyLength = historyOneDay[0]['latency'].length;
	for (l = 0; l < latencyLength; ++l) {
		var data = 0;

		for (o = 0; o < historyOneDay.length; ++o) {
			var server = historyOneDay[o];

			if ((urls.length > 0) && (urls.indexOf(server.url) === -1)) {
				continue;
			}

			if (data === 'off') {
				continue;
			}
			if (server['latency'][l] === 'null') {
				continue;
			} else if (server['latency'][l] === 'off') {
				data = 'off';
			} else {
				data = Math.max(data, server['latency'][l]);
			}
		}

		line = document.createElementNS(svgns, 'line');
		line.setAttributeNS(null, 'x1', l + 1);
		line.setAttributeNS(null, 'x2', l + 1);
		line.setAttributeNS(null, 'y2', 100);
		if (data === 'off') {
			line.setAttributeNS(null, 'y1', 1);
			line.setAttributeNS(null, 'stroke', '#dc3545');
		} else {
			if (data > 0) {
				line.setAttributeNS(null, 'y1', 100 - Math.max(data * 10, 3));
			} else {
				line.setAttributeNS(null, 'y1', 100 - data * 10);
			}

			if (data >= 10) {
				line.setAttributeNS(null, 'stroke', '#dc3545');
			} else if (data >= 2.5) {
				line.setAttributeNS(null, 'stroke', '#ffc107');
			} else {
				line.setAttributeNS(null, 'stroke', '#198754');
			}
		}
		svg.appendChild(line);
	}
}

function setFilter() {
	var oneDay = historyOneDay;
	var filterItems = '';
	var descriptions = {
		'/start/': 'Startseite',
		'/moodle/': 'Verbund-Auswahl',
		'/moodle/login/index.php': 'Portal-Login-Seite',
		'/bbb/': 'BigBlueButton',
		'/v1/': 'Verbund 1 - Login-Seite',
		'/v1/my/': 'Verbund 1 - Dashboard',
		'/v1und2/': 'Verbund 1 und 2 - Login-Seite',
		'/v1und2/my/': 'Verbund 1 und 2 - Dashboard',
		'/v2/': 'Verbund 2 - Login-Seite',
		'/v2/my/': 'Verbund 2 - Dashboard',
		'/v3/': 'Verbund 3 - Login-Seite',
		'/v3/my/': 'Verbund 3 - Dashboard',
		'/v3und4/': 'Verbund 3 und 4 - Login-Seite',
		'/v3und4/my/': 'Verbund 3 und 4 - Dashboard',
		'/v4/': 'Verbund 4 - Login-Seite',
		'/v4/my/': 'Verbund 4 - Dashboard',
		'/osz/': 'Weitere Bereiche - Login-Seite',
		'/osz/my/': 'Weitere Bereiche  - Dashboard'
	};

	oneDay.sort(function(a, b) {
		var urlA = a.url;
		var urlB = b.url;

		if (0 === urlA.indexOf('/osz')) {
			urlA = 'v' + urlA;
		}
		if (0 === urlB.indexOf('/osz')) {
			urlB = 'v' + urlB;
		}

		if (urlA < urlB) {
			return -1;
		}
		if (urlA > urlB) {
			return 1;
		}
	});

	var firstV = false;
	for (o = 0; o < oneDay.length; ++o) {
		var server = oneDay[o];

		if (server.url) {
			if (!firstV && (0 === server.url.indexOf('/v'))) {
				firstV = true;
				filterItems += '<option disabled>&nbsp;</option>';
			}
			var title = server.url;
			if (descriptions[title]) {
				title = descriptions[title];
			}
			if (server.url !== '/bbb/') {
				filterItems += '<option value="' + server.url + '">' + title + '</options>';
			}
		}
	}
	if (filterItems !== '') {
		filterItems = '<option disabled>&nbsp;</option>' + filterItems;
		filterItems = '<option selected value="">Zeige den kompletten Lernraum an</options>' + filterItems;
	}

	var urlVal = $('#selectFilter').val();
	$('#selectFilter').html(filterItems);
	$('#selectFilter').prop('disabled', filterItems === '');
	$('#selectFilter').val(urlVal);

	urlVal = $('#selectFilter').val();
	if (urlVal === null) {
		$('#selectFilter').val('');
	}
}

function setNavigation() {
	var days = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
	var months = ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
	var day = days[historyDate.getDay()];
	var month = months[historyDate.getMonth()];

	$('#goback').prop('disabled', historyDate.getTime() <= historyMin.getTime());
	$('#gonext').prop('disabled', historyDate.getTime() >= historyMax.getTime());
	$('#todayweekday').text(day);
	$('#today').text(historyDate.getDate() + '. ' + month + ' ' + historyDate.getFullYear());
}

function loadDataHistory() {
	var today = historyDate.toJSON().slice(0,10);

	$.ajax({
		url: '/data/today-' + today + '.json',
		success: function(result) {
			historyOneDay = result.today;
			setFilter();
			paintSVG();
		},
		error: function(response) {
			console.log(response.responseText);
			historyOneDay = [{
				latency: [],
				url: ''
			}];
			setFilter();
			paintSVG();
		}
	});

	setNavigation();
}

function loadData() {
	historyMax = new Date();
    historyMax.setMinutes(historyMax.getMinutes() - historyMax.getTimezoneOffset());
	historyMax.setSeconds(0);
	historyMax.setMilliseconds(0);
	historyMax.setHours(historyDate.getHours());
	historyMax.setMinutes(historyDate.getMinutes());

	$.ajax({
		url: "/data/servers.json",
		success: function(result) {
			processServersJSON(result);
			loadDataHistory();
		}
	});

	setTimeout(loadData, 5 * 60 * 1000);
}

function goBack() {
	historyDate.setDate(historyDate.getDate() - 1);
	loadDataHistory();

	return false;
}

function goNext() {
	historyDate.setDate(historyDate.getDate() + 1);
	loadDataHistory();

	return false;
}

function goFilter() {
	paintSVG();
}

$(document).ready(function () {
	historyDate = new Date();
    historyDate.setMinutes(historyDate.getMinutes() - historyDate.getTimezoneOffset());
	historyDate.setSeconds(0);
	historyDate.setMilliseconds(0);
	historyMin.setHours(historyDate.getHours());
	historyMin.setMinutes(historyDate.getMinutes());

	loadData();
});