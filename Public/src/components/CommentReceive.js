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
              <p className="card-box">{this.props.comment.p_user_id ? this.props.comment.p_content : this.props.comment.text}</p>
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
              {
                this.state.reply ?
                <li className="comment_item">
                  <div className="comment_avator"></div>
                  <div className="comment_content">
                    <span className="comment_name">
                      <a href="javascript:;">{this.state.reply.user_id}：</a>
                    </span>
                    <span className="comment_text">{this.state.reply.content}</span>
                    <div className="comment_option">
                      <span className="comment_time">{getTimeString(this.state.reply.ctime * 1000)}</span>
                    </div>
                  </div>
                </li> :
                <li className="comment_item">
                  <div className="comment_avator"></div>
                  <div className="comment_content">
                    <div className="comment_reply_box clearfix">
                      <textarea placeholder={'回复@' + this.props.comment.user_id} ref="textarea" rows="2"></textarea>
                      <a href="javascript:;" onClick={this.handleReply}>评论</a>
                    </div>
                  </div>
                </li>
              }
            </ul> : null
          }
        </div>
      </div>
    );
  },

  // 展开收起回复框
  handleToggleReply: function(e) {
    this.setState(assign({}, this.state, {
      isReplying: !this.state.isReplying
    }));
  },

  // 回复评论
  handleReply: function(e) {
    var sid = this.props.comment.s_id;
    var pid = this.props.comment.p_pid;
    var content = this.refs.textarea.value;
    $.ajax({
      type: 'post',
      url: url.do_comment,
      data: {
        sid: sid,
        pid: pid,
        content: content
      },
      success: function(data) {
        this.setState(assign({}, this.state, {
          reply: data
        }));
      }.bind(this)
    });
  }
});

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
