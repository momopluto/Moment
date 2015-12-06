var React = require('react');
var MomentList = require('./MomentList');

var Collect = React.createClass({
  render: function() {
    var moments = this.props.moments;
    return (
      <div>
        <div className="card">
            <h2 className="card-title">
              <b>我的收藏</b>
              <span>{moments.length}</span>
            </h2>
        </div>
        <MomentList moments={moments} />
      </div>
    );
  }
});

module.exports = Collect;
