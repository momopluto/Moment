var React = require('react');

var CommentReceive = React.createClass({
  render: function() {
    return (
      <div>
        <div className="cradWrap">
            <div className="cardContent">
                <div className="cardIcon"></div>
                <article className="cardDetail">
                    <p className="card_title">一起来吐槽</p>
                    <p className="card_text">评论我的微博：红包啊红包，你慢点飞嘛！</p>
                    <div className="medaiBox">
                        <p className="card-box">#让红包飞#当你看到这条微博时，马年的好运气已经降临到你头上啦！新年快乐，恭喜发财，抢红包这事儿，还得大家一起来。小伙伴们快来评论我的微博领红包，10亿新春好礼等你抢！ @小鳗鱼仔-J珊珊 @love文字的函 @烨子li 马上抽奖</p>
                    </div>
                    <p className="card_time">40分钟前</p>
                </article>
            </div>
            <div className="cardHandle">
                <ul className="rowLine clearfix">
                    <li>
                        <a className="row_btn" href="javascript:;">回复</a>
                    </li>
                </ul>
                <ul className="comment_list" style={{display: 'none'}}>
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

        <div className="cradWrap">
            <div className="cardContent">
                <div className="cardIcon"></div>
                <article  className="cardDetail">
                    <p className="card_title">一起来吐槽</p>
                    <p className="card_text">评论我的微博：红包啊红包，你慢点飞嘛！</p>
                    <div className="medaiBox">
                        <p className="card-box">#让红包飞#当你看到这条微博时，马年的好运气已经降临到你头上啦！新年快乐，恭喜发财，抢红包这事儿，还得大家一起来。小伙伴们快来评论我的微博领红包，10亿新春好礼等你抢！ @小鳗鱼仔-J珊珊 @love文字的函 @烨子li 马上抽奖：http://t.cn/8ka4t9y</p>
                    </div>
                    <p className="card_time">40分钟前</p>
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
      </div>
    );
  }
});

module.exports = CommentReceive;
