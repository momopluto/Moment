function getTimeString (time) {
	var delta = new Date().getTime() - new Date(parseInt(time)).getTime();
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
		return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + ' ' + date.getHours() + ':' + date.getMinutes();
	}
}

exports.getTimeString = getTimeString;