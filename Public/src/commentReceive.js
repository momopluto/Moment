var React = require('react');
var ReactDOM = require('react-dom');
var CommentReceive = require('./components/CommentReceive');

ReactDOM.render(
  <CommentReceive comments={JSON.parse(json_data)} />,
  document.getElementById('comment-receive-container')
);
