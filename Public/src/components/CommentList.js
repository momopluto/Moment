var React = require('react');
var assign = require('object-assign');
var getTimeString = require('../util/util').getTimeString;

var Comment = React.createClass({
	getInitialState: function() {
		return {
			isReplying: false
		};
	},

	render: function() {
		var comment = this.props.comment;
		console.log(comment);
		return (
			<li className="comment_item">
				<div className="comment_avator"></div>
				<div className="comment_content">
					<span className="comment_name">
						<a href="javascript:;">{comment.c_id}</a>
						{ comment.pid > 0 ? '回复' : null }
						{
							comment.pid > 0 ? <a href="##">{comment.pid}</a> : null
						}
					</span>
					：
					<span className="comment_text">{comment.content}</span>
					<div className="comment_option">
						<span className="comment_time">{getTimeString(comment.ctime * 1000)}</span>
						<a className="comment_reply" href="javascript:;" onClick={this.handleToggleReply}>回复</a>
					</div>
					{
						this.state.isReplying ?
						<div className="comment_reply_box clearfix">
							<textarea placeholder={"回复@" + this.props.moment.s_id} rows="2"></textarea>
							<a href="javascript:;" onClick={this.handleDoComment}>评论</a>
						</div> : null
					}
				</div>
			</li>
		);
	},

	handleToggleReply: function(e) {
		this.setState(assign({}, this.state, {
			isReplying: !this.state.isReplying
		}));
	},

	handleDoComment: function(e) {
		var content = e.target.previousSibling.value;
		var pid = this.props.comment.c_id;
		this.props.doComment(content, pid);
		this.handleToggleReply();
	}
});

var CommentList = React.createClass({
	render: function() {
		return (
			<ul className="comment_list">
				<div className="comment_reply_box clearfix">
					<textarea placeholder={'回复@' + this.props.moment.user_id} rows="2"></textarea>
					<a href="javascript:;" onClick={this.handleDoComment}>评论</a>
				</div>
				{
					this.props.comments.map(function(item, index) {
						return <Comment comment={item} key={index} moment={this.props.moment} doComment={this.props.doComment} />;
					}.bind(this))
				}
			</ul>
		);
	},

	// 评论分享
	handleDoComment: function(e) {
		var content = e.target.previousSibling.value;
		var pid = 0;
		this.props.doComment(content, pid);
	}
});

module.exports = CommentList;
