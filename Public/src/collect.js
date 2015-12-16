var React = require('react');
var ReactDOM = require('react-dom');
var Collect = require('./components/Collect');

ReactDOM.render(
  <Collect moments={JSON.parse(json_data)} />,
  document.getElementById('collect-container')
);
