var React = require('react');
var ReactDOM = require('react-dom');
var MyMoment = require('./components/MyMoment');

ReactDOM.render(
	<MyMoment moments={JSON.parse(json_data)} />,
	document.getElementById('my-moment-container')
);