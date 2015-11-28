var React = require('react');

var CommentList = React.createClass({
	render: function() {
		return (
			<ul className="comment_list">
				<li className="comment_item">
					<div className="comment_avator"></div>
					<div className="comment_content">
						<span className="comment_name">
							<a href="##">你好杨小米</a>
							回复
							<a href="##">@怜逐</a>
						</span>
						:
						<span className="comment_text">做你闺蜜嘛</span>
						<div className="comment_option">
							<span className="comment_time">39分钟前</span>
							<a className="comment_reply" href="javascript:;">回复</a>
						</div>
					</div>
				</li>
				<li className="comment_item">
					<div className="comment_avator"></div>
					<div className="comment_content">
						<span className="comment_name">
							<a href="##">你好杨小米</a>
							回复
							<a href="##">@怜逐</a>
						</span>
						:
						<span className="comment_text">年轻时你或许并不真正需要闺蜜，年华大好，青春扑面而来，光一个恋爱就够忙活几年。过了30你再看，那些为爱情断了友情的姑娘，守在无涯等待中，哭成一朵雨天的花。爱情让你流下的泪，友情帮你擦干它。真正的姐妹，比恋人更能天长地久。最好的闺蜜，是你的另一个自己—苏岑。</span>
						<div className="comment_option">
							<span className="comment_time">39分钟前</span>
							<a className="comment_reply" href="javascript:;">回复</a>
						</div>
						<div className="comment_reply_box clearfix">
							<textarea placeholder="回复@怜逐" rows="2"></textarea>
							<a href="javascript:;">评论</a>
						</div>
					</div>
				</li>
				<li className="comment_item">
					<div className="comment_avator"></div>
					<div className="comment_content">
						<span className="comment_name">
							<a href="##">你好杨小米</a>
							回复
							<a href="##">@怜逐</a>
						</span>
						:
						<span className="comment_text">做你闺蜜嘛</span>
						<div className="comment_option">
							<span className="comment_time">39分钟前</span>
							<a className="comment_reply" href="javascript:;">回复</a>
						</div>
					</div>
				</li>
			</ul>
		);
	}
});

module.exports = CommentList;