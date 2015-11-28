var React = require('react');
var Moment = require('./Moment');

var MomentList = React.createClass({
	propTypes: {
		moments: React.PropTypes.array.isRequired
	},

	render: function() {
		var momentList = this.props.moments.map(function(item, index) {
			return <Moment key={index} moment={item} />;
		});

		return (
			<div>{momentList}</div>
		);
	}
});

module.exports = MomentList;
