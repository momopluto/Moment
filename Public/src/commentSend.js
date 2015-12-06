var React = require('react');
var ReactDOM = require('react-dom');
var CommentSend = require('./components/CommentSend');

ReactDOM.render(
  <CommentSend comments={JSON.parse(json_data)} />,
  document.getElementById('comment-receive-container')
);
