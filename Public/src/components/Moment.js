var React = require('react');
var CommentList = require('./CommentList');
var assign = require('object-assign');
var getTimeString = require('../util/util').getTimeString;

var Moment = React.createClass({
	propTypes: {
		moment: React.PropTypes.object
	},

	getInitialState: function() {
		return {
			isOpeningComment: false,
			isZoomingOut: false,
			zoomInIndex: 0
		};
	},

	render: function() {
		var moment = this.props.moment;

		// 不公开，则隐藏
		if (moment.ispublic == 0) {
			return null;
		}

		var imgs = moment.imgs.length > 0 ? moment.imgs.split(',').map(function(item, index) {
			return <li className="picItem" key={index} data-id={index} onClick={this.handlePicZoomIn} style={{backgroundImage: 'url(' + picPath + moment.imgpath + '/' + item + ')'}}></li>;
		}.bind(this)) : null;

		var picZoomIn = this.state.isZoomingOut ? (
			<div className="pic_zoom_in">
				{this.state.zoomInIndex > 0 ? <a className="iconfont pic_prev" href="javascript:;" onClick={this.handlePrevPic}>&#xe604;</a> : null}
				{this.state.zoomInIndex < imgs.length - 1 ? <a className="iconfont pic_next" href="javascript:;"  onClick={this.handleNextPic}>&#xe605;</a> : null}
				<img src={picPath + moment.imgpath + '/' + moment.imgs.split(',')[this.state.zoomInIndex]} onClick={this.handlePicZoomOut} />
			</div>
		) : null;

		return (
			<div className="cradWrap" data-id={moment.s_id}>
				<div className="cardContent">
					<dic className="cardIcon"></dic>
					<article className="cardDetail">
						<p className="card_title">{moment.user_id}</p>
						<p className="card_text">{moment.text}</p>
						<div className="medaiBox">
							<ul className="mediaPics clearfix">
								{imgs}
							</ul>
						</div>
						<p className="card_time">{getTimeString(moment.ctime)}</p>
					</article>
				</div>
				{picZoomIn}
				<div className="cardHandle">
					<ul className="rowLine clearfix">
						<li className={moment.collected == 0 ? '' : 'on'}>
							<a className="row_btn" href="javascript:;" onClick={this.handleCollect}>
								<i className="iconfont">&#xe600;</i>
								收藏
								<span className="bubble bubble-add">收藏成功</span>
								<span className="bubble bubble-sub">取消收藏</span>
							</a>
						</li>
						<li>
							<a className="row_btn" href="javascript:;" onClick={this.handleOpenComment}><i className="iconfont">&#xe602;</i> 评论 <i>{moment.cmt_count}</i></a>
						</li>
						<li className={moment.thumbed == 0 ? '' : 'on'}>
							<a className="row_btn" href="javascript:;" onClick={this.handleThumb}><i className="iconfont">&#xe601;</i> 赞 <i>{moment.tb_count}</i></a>
						</li>
					</ul>
					{this.state.isOpeningComment ? <CommentList comments={this.state.comments} doComment={this.doComment} moment={moment} /> : null}
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
	},

	// 展开评论列表
	handleOpenComment: function(e) {
		$.ajax({
			type: 'post',
			url: url.get_comment,
			data: {
				sid: this.props.moment.s_id
			},
			success: function(data) {
				if (data && !data.errcode) {
					this.setState(assign({}, this.state, {
						isOpeningComment: !this.state.isOpeningComment,
						comments: data
					}));
				} else {
					this.setState(assign({}, this.state, {
						isOpeningComment: !this.state.isOpeningComment,
						comments: []
					}));
				}
			}.bind(this)
		});
	},

	// 评论分享
	doComment: function(content, pid) {
		if (!content) {
			return alert('评论不能为空');
		}
		$.ajax({
			type: 'post',
			url: url.do_comment,
			data: {
				sid: this.props.moment.s_id,
				pid: pid,
				content: text
			},
			success: function(data) {
				if (!data.errcode) {
					this.setState(assign({}, state, {
						comments: [data].concat(this.state.comments)
					}));
				}
			}.bind(this)
		});
	},

	// 收藏分享
	handleCollect: function(e) {
		if (this.props.moment.collected == 1) {
			$.ajax({
				type: 'post',
				url: url.uncollect,
				data: {
					sid: this.props.moment.s_id
				},
				success: function() {
					this.props.collectMoment(this.props.moment.s_id);
				}.bind(this)
			});
		} else {
			$.ajax({
				type: 'post',
				url: url.collect,
				data: {
					sid: this.props.moment.s_id
				},
				success: function() {
					this.props.collectMoment(this.props.moment.s_id);
				}.bind(this)
			});
		}
	},

	// 点赞分享
	handleThumb: function(e) {
		if (this.props.moment.thumbed == 1) {
			$.ajax({
				type: 'post',
				url: url.unthumb,
				data: {
					sid: this.props.moment.s_id
				},
				success: function() {
					this.props.thumbMoment(this.props.moment.s_id);
				}.bind(this)
			});
		} else {
			$.ajax({
				type: 'post',
				url: url.thumb,
				data: {
					sid: this.props.moment.s_id
				},
				success: function() {
					this.props.thumbMoment(this.props.moment.s_id);
				}.bind(this)
			});
		}
	}
});

module.exports = Moment;
