var React = require('react');
var ReactDOM = require('react-dom');
var ThumbList = require('./components/ThumbList');

ReactDOM.render(
  <ThumbList thumbs={JSON.parse(json_data)} type="send" />,
  document.getElementById('thumb-send-container')
);
