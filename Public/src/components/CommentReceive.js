var React = require('react');
var getTimeString = require('../util/util').getTimeString;

var CommentReceiveItem = React.createClass({
  render: function() {
    return (
      <div className="cradWrap">
        <div className="cardContent">
          <div className="cardIcon"></div>
          <article  className="cardDetail">
            <p className="card_title">{this.props.comment.user_id}</p>
            <p className="card_text">评论我的微博：{this.props.comment.content}</p>
            <div className="medaiBox">
              <p className="card-box">{this.props.comment.text}</p>
            </div>
            <p className="card_time">{getTimeString(this.props.comment.commenttime)}</p>
          </article>
        </div>
        <div className="cardHandle">
          <ul className="rowLine clearfix">
            <li>
              <a className="row_btn" href="javascript:;">回复</a>
            </li>
          </ul>
          <ul className="comment_list">
            <li className="comment_item">
              <div className="comment_avator"></div>
              <div className="comment_content">
                <div className="comment_reply_box clearfix">
                  <textarea placeholder="回复@怜逐" rows="2"></textarea>
                  <a href="javascript:;">评论</a>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    );
  }
})

var CommentReceive = React.createClass({
  render: function() {
    return (
      <div>
        {
          this.props.comments.map(function(item, index) {
            return <CommentReceiveItem comment={item} key={index} />;
          })
        }
      </div>
    );
  }
});

module.exports = CommentReceive;
