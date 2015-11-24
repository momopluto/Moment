var React = require('react');
var assign = require('object-assign');
var getTimeString = require('../util/util').getTimeString;

var Moment = React.createClass({
	propTypes: {
		moment: React.PropTypes.object
	},

	getInitialState: function() {
		return {
			isZoomingOut: false,
			zoomInIndex: 0
		};
	},

	render: function() {
		var moment = this.props.moment;

		var pics = moment.pics.map(function(item, index) {
			return <li className="picItem" key={index} data-id={index} onClick={this.handlePicZoomIn} style={{backgroundImage: 'url(' + item + ')'}}></li>;
		}.bind(this));

		var picZoomIn = this.state.isZoomingOut ? (
			<div className="pic_zoom_in">
				{this.state.zoomInIndex > 0 ? <a className="iconfont pic_prev" href="javascript:;" onClick={this.handlePrevPic}>&#xe604;</a> : null}
				{this.state.zoomInIndex < moment.pics.length - 1 ? <a className="iconfont pic_next" href="javascript:;"  onClick={this.handleNextPic}>&#xe605;</a> : null}
				<img src={moment.pics[this.state.zoomInIndex]} onClick={this.handlePicZoomOut} />
			</div>
		) : null;

		return (
			<div className="cradWrap">
				<div className="cardContent">
					<dic className="cardIcon"></dic>
					<article className="cardDetail">
						<p className="card_title">一起来吐槽</p>
						<p className="card_text">{moment.text}</p>
						<div className="medaiBox">
							<ul className="mediaPics clearfix">
								{pics}
							</ul>
						</div>
						<p className="card_time">{getTimeString(moment.createAt)}</p>
					</article>
				</div>
				{picZoomIn}
				<div className="cardHandle">
					<ul className="rowLine clearfix">
						<li className="on">
							<a className="row_btn" href="javascript:void(0);">
								<i className="iconfont"></i>
								收藏
								<span className="bubble bubble-add">收藏成功</span>
								<span className="bubble bubble-sub">取消收藏</span>
							</a>
						</li>
						<li>
							<a className="row_btn" href="javascript:void(0);"><i className="iconfont"></i> 评论 <i>1</i></a>
						</li>											
						<li>
							<a className="row_btn" href="javascript:void(0);"><i className="iconfont"></i> 赞 <i>5</i></a>
						</li>																
					</ul>
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
				</div>
			</div>
		);
	},

	handlePicZoomIn: function(e) {
		var target = e.srcElement || e.target;
		var index = target.getAttribute('data-id');
		this.setState(assign({}, this.state, {
			isZoomingOut: true,
			zoomInIndex: index
		}));
	},

	handlePicZoomOut: function(e) {
		this.setState(assign({}, this.state, {
			isZoomingOut: false
		}));
	},

	handlePrevPic: function(e) {
		this.setState(assign({}, this.state, {
			zoomInIndex: --this.state.zoomInIndex
		}));
	},

	handleNextPic: function(e) {
		this.setState(assign({}, this.state, {
			zoomInIndex: ++this.state.zoomInIndex
		}));
	}
});

module.exports = Moment;
