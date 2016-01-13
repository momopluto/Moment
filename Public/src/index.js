var React = require('react');
var ReactDOM = require('react-dom');
var App = require('./containers/App');

ReactDOM.render(
    <App moments={JSON.parse(window.json_data)} />,
    document.getElementById('moment-container')
);
