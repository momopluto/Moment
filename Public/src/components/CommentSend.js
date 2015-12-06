var React = require('react');
var getTimeString = require('../util/util').getTimeString;

var CommentSendItem = React.createClass({
  render: function() {
    var comment = this.props.comment;

    return (
      <div className="cradWrap card">
        <div className="cardContent">
          <dic className="cardIcon"></dic>
          <article className="cardDetail">
            <p className="card_title">{comment.user_id}</p>
            <p className="card_text">{comment.content}</p>
            <div className="medaiBox">
              {
                comment.p_content ?
                <p className="card-box">回复{comment.p_user_id}的评论：{comment.p_content}</p> :
                <p className="card-box">评论{comment.p_user_id}的分享：{comment.text}</p>
              }
            </div>
            <p className="card_time">{getTimeString(comment.ctime)}</p>
          </article>
        </div>
        <div className="card-self">
          <a href="javascript:;">删除</a>
        </div>
      </div>
    );
  }
});

var CommentSend = React.createClass({
  render: function() {
    var comments = this.props.comments;
    return (
      <div>
        {
          comments.map(function(item, index) {
            return <CommentSendItem comment={item} key={index} />;
          })
        }
      </div>
    );
  }
});

module.exports = CommentSend;
