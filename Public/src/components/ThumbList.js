var React = require('react');
var getTimeString = require('../util/util').getTimeString;

var Thumb = React.createClass({
  render: function() {
    var thumb = this.props.thumb;
    var type = this.props.type;
    return (
      <div className="cradWrap">
        <div className="cardContent">
          <dic className="cardIcon"></dic>
          <article  className="cardDetail">
            <p className="card_title"><a href="">{thumb.user_id}</a></p>
            <div className="medaiBox">
              <p className="card-box">赞了我的分享：{thumb.text}</p>
            </div>
            <p className="card_time">{getTimeString(thumb.ctime)}</p>
          </article>
        </div>
      </div>
    );
  }
});

var ThumbList = React.createClass({
  render: function() {
    var thumbs = this.props.thumbs;
    var type = this.props.type;
    return (
      <div>
        <div className="card card-header">
            <a className={"card-header-item" + (type == 'receive' ? ' on' : '')} href={url.thumb_receive}>收到的赞</a>
            <a className={"card-header-item" + (type == 'send' ? ' on' : '')} href={url.thumb_send}>发出的赞</a>
        </div>
        {
          thumbs.map(function(item, index) {
            return <Thumb thumb={item} type={type} key={index} />;
          })
        }
      </div>
    );
  }
});

module.exports = ThumbList;
