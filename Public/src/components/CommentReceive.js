var React = require('react');
var assign = require('object-assign');
var getTimeString = require('../util/util').getTimeString;

var CommentReceiveItem = React.createClass({
  getInitialState: function() {
    return {
      isReplying: false
    };
  },

  render: function() {
    return (
      <div className="cradWrap">
        <div className="cardContent">
          <div className="cardIcon"></div>
          <article  className="cardDetail">
            <p className="card_title">{this.props.comment.user_id}</p>
            {
              this.props.comment.p_user_id ?
              <p className="card_text">回复我的评论：{this.props.comment.content}</p> :
              <p className="card_text">评论我的微博：{this.props.comment.content}</p>
            }
            <div className="medaiBox">
              <p className="card-box">{this.props.comment.text}</p>
            </div>
            <p className="card_time">{getTimeString(this.props.comment.commenttime)}</p>
          </article>
        </div>
        <div className="cardHandle">
          <ul className="rowLine clearfix">
            <li>
              <a className="row_btn" href="javascript:;" onClick={this.handleToggleReply}>{this.state.isReplying ? '收起' : '回复'}</a>
            </li>
          </ul>
          {
            this.state.isReplying ?
            <ul className="comment_list">
              <li className="comment_item">
                <div className="comment_avator"></div>
                <div className="comment_content">
                  <div className="comment_reply_box clearfix">
                    <textarea placeholder={'回复@' + this.props.comment.user_id} rows="2"></textarea>
                    <a href="javascript:;">评论</a>
                  </div>
                </div>
              </li>
            </ul> : null
          }
        </div>
      </div>
    );
  },

  handleToggleReply: function(e) {
    this.setState(assign({}, this.state, {
      isReplying: !this.state.isReplying
    }));
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
