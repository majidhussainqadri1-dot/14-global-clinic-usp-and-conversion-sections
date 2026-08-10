(function () {
	'use strict';

	if (!window.GCU_PUBLIC || !window.GCU_PUBLIC.consent || !window.GCU_PUBLIC.endpoint) {
		return;
	}

	if (window.navigator && window.navigator.globalPrivacyControl === true) {
		return;
	}

	if (window.navigator && window.navigator.connection && window.navigator.connection.saveData === true) {
		return;
	}

	function send(stage, destination, token) {
		if (!token) {
			return;
		}

		var eventId = (window.crypto && window.crypto.randomUUID) ? window.crypto.randomUUID() : '';
		if (!eventId) {
			return;
		}

		var body = JSON.stringify({
			event_id: eventId,
			stage: stage,
			destination: destination || ''
		});

		fetch(window.GCU_PUBLIC.endpoint, {
			method: 'POST',
			credentials: 'same-origin',
			keepalive: true,
			headers: {
				'Content-Type': 'application/json',
				'X-GCU-Event-Token': token
			},
			body: body
		}).catch(function () {
			// Measurement is non-blocking and never changes the user journey.
		});
	}

	var page = document.querySelector('[data-gcu-impression-token]');
	if (page) {
		var impressionToken = page.getAttribute('data-gcu-impression-token');
		page.removeAttribute('data-gcu-impression-token');
		send('impression', '', impressionToken);
	}

	document.addEventListener('click', function (event) {
		var link = event.target.closest('[data-gcu-event-token][data-gcu-destination]');
		if (!link) {
			return;
		}
		var token = link.getAttribute('data-gcu-event-token');
		var destination = link.getAttribute('data-gcu-destination');
		link.removeAttribute('data-gcu-event-token');
		send('cta_selected', destination, token);
	}, { passive: true });
}());
