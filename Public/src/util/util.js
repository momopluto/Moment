function getTimeString (time) {
	var delta = new Date() - new Date(time);
	if (delta < 1000) {
		return '刚刚';
	} else if (delta < 1000 * 60) {
		return Math.round(delta / 1000) + '秒前';
	} else if (delta < 1000 * 60 * 60) {
		return Math.round(delta / 1000 / 60) + '分钟前';
	} else if (delta < 1000 * 60 * 60 * 24) {
		return Math.round(delta / 1000 / 60 / 60) + '小时前';
	} else {
		var date = new Date(time);
		return date.getFullYear() + '-' + _2(date.getMonth() + 1) + '-' + _2(date.getDate()) + ' ' + _2(date.getHours()) + ':' + _2(date.getMinutes());
	}
}

function _2(n) {
	if (n < 10) return '0' + n;
	else return n;
}

exports.getTimeString = getTimeString;
