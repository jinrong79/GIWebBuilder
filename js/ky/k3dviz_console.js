(function() {
	var server_address = "https://apps.kbitc.com:8443";
	var sio_address = "https://apps.kbitc.com:8443/players";

	function login(username, password, cb) {
		$.ajax({
			url: server_address + "/api/auth/login",
			method: "POST",
			data: {
				username: username,
				password: password
			}
		}).done(function(res) {
			if (typeof cb === 'function') {
				if (res.code === 0) {
					cb.call(null, null, res.data);
				}
				else {
					cb.call(null, new Error(res.message));
				}
			}
		}).error(function(e) {
			if (typeof cb === 'function') {
				cb.call(null, e);
			}
		});
	}

	function logout(cb) {
		$.ajax({
			url: server_address + "/api/auth/logout",
			method: "POST"
		}).done(function(res) {
			if (typeof cb === 'function') {
				if (res.code === 0) {
					cb.call(null, null);
				}
				else {
					cb.call(null, new Error(res.message));
				}
			}
		}).error(function(e) {
			if (typeof cb === 'function') {
				cb.call(null, e);
			}
		});
	}

	function connect(access_token) {
		var socket = io(sio_address, {
			forceNew: true,
			query: {
				access_token: access_token
			},
			transports: ['websocket', 'polling'],
			transportOptions: {
				websocket: {
					extraHeaders: {
						'Authorization': 'Bearer ' + access_token
					}
				}
			}
		});
		
		socket.on('connect', function() {
			console.log('connected');
		});

		socket.on('message', function(data) {
			console.log(data);
		});

		socket.on('disconnect', function() {
			console.log('disconnected');
		});

		socket.on('error', function(e) {
			console.log(e);
		});

		return socket;
	}

	var socket = null;
	function init(username, password, cb) {
		login(username, password, function(err, res) {
			if (err) {
				if (typeof cb === 'function') {
					cb.call(null, err);
				}
				return;
			}

			socket = connect(res.sid);
			if (typeof cb === 'function') {
				function helo(destination) {
					socket.emit('message', JSON.stringify({
						"type": "CTRL-HELO",
						"source": res.cid,
						"destination": destination,
						"timestamp": new Date()
					}));
				}

				function bye(destination) {
					socket.emit('message', JSON.stringify({
						"type": "CTRL-BYE",
						"source": res.cid,
						"destination": destination,
						"timestamp": new Date()
					}));
				}

				function send(destination, data) {
					socket.emit('message', JSON.stringify({
						"type": "CTRL",
						"source": res.cid,
						"destination": destination,
						"timestamp": new Date(),
						"data": data
					}));
				}

				function send_cctv_to_screen(camera_code, rtspurl) {
					socket.emit('message', JSON.stringify({
						"type": "CCTV2Display",
						"source": res.cid,
						"channel": "displays",
						"destination": "VerticalScreen",
						"timestamp": new Date(),
						"data": {
							"code": camera_code,
							"url": rtspurl
						}
					}));
				}

				cb.call(null, null, {
					cid: res.cid,
					sid: res.sid,
					socket: socket,
					helo: helo,
					bye: bye,
					send: send,
					send_cctv_to_screen
				});
			}
		});
	}

	function deinit(cb) {
		if (socket) {
			socket.disconnect();
			socket = null;
		}

		logout(cb);
	}

	// 加载所有可控制屏幕实例
	function load_screen(cb) {
		$.ajax({
			url: server_address + "/api/player?controllable=1",
			method: "GET"
		}).done(function(res) {
			if (typeof cb === 'function') {
				if (res.code === 0) {
					cb.call(null, null, res.data);
				}
				else {
					cb.call(null, new Error(res.message));
				}
			}
		}).error(function(e) {
			if (typeof cb === 'function') {
				cb.call(null, e);
			}
		});
	}

	// 加载所有机器人列表
	function load_robots(cb) {
		$.ajax({
			url: server_address + "/api/thirdparty/harirobot/list",
			method: "GET"
		}).done(function(res) {
			if (typeof cb === 'function') {
				if (res.code === 0) {
					cb.call(null, null, res.data.robots);
				}
				else {
					cb.call(null, new Error(res.message));
				}
			}
		}).error(function(e) {
			if (typeof cb === 'function') {
				cb.call(null, e);
			}
		});
	}

	// 加载所有监控摄像头列表
	function load_cctv_cameras(cb) {
		$.ajax({
			url: server_address + "/api/thirdparty/cloudivs/cameras",
			method: "GET"
		}).done(function(res) {
			if (typeof cb === 'function') {
				if (res.code === 0) {
					cb.call(null, null, res.data.cameras);
				}
				else {
					cb.call(null, new Error(res.message));
				}
			}
		}).error(function(e) {
			if (typeof cb === 'function') {
				cb.call(null, e);
			}
		});
	}

	// 获取指定摄像头播放地址
	function load_cctv_rtspurl(camera_codes, cb) {
		if (!(camera_codes instanceof Array)) {
			camera_codes = [camera_codes];
		}

		$.ajax({
			url: server_address + "/api/thirdparty/cloudivs/rtspurls",
			method: "POST",
			data: {
				"cameraCodes": camera_codes,
				"mediaURLParam": {
					"serviceType": 1,
					"protocolType": 2
				}
			},
			dataType: "json"
		}).done(function(res) {
			if (typeof cb === 'function') {
				if (res.code === 0) {
					cb.call(null, null, res.data);
				}
				else {
					cb.call(null, new Error(res.message));
				}
			}
		}).error(function(e) {
			if (typeof cb === 'function') {
				cb.call(null, e);
			}
		});
	}

	window.CM = {
		init,
		deinit,
		load_screen,
		load_robots,
		load_cctv_cameras,
		load_cctv_rtspurl
	};
})();