(function() {

	var el = wp.element.createElement;

	wp.blocks.registerBlockType( 'trx_addons/title', {
		
		title: 'Title',

		keywords: ['title', 'trx_addons'],

		icon: 'universal-access-alt',

		category: 'layout',

		anchor: true,			// Add field 'ID' to direct link of this block. Default is false

		customClassName: true,	// Add field 'class' of this block. Default is true

		attributes: {
			title: {
				type: 'string',
				source: 'html',
				selector: '.sc_item_title',
			},
			subtitle: {
				type: 'string',
				source: 'html',
				selector: '.sc_item_subtitle',
			},
			description: {
				type: 'string',
				source: 'html',
				selector: '.sc_item_description',
			},
		},
		
		transforms: {
			from: [
				{
					type: 'block',
					blocks: [ 'core/paragraph', 'core/heading' ],
					transform: function ( content ) {
						return createBlock( 'trx_addons/title', {
							content,
						} );
					},
				},
			],
			to: [
				{
					type: 'block',
					blocks: [ 'core/paragraph' ],
					transform: function ( content ) {
						return createBlock( 'core/paragraph', {
							content,
						} );
					},
				},
				{
					type: 'block',
					blocks: [ 'core/heading' ],
					transform: function ( content ) {
						return createBlock( 'core/heading', {
							content,
						} );
					},
				},
			],
		},
		
		edit( props ) {
			var children = [];
			if ( props.attributes.subtitle !== '' ) {
				children.push(
					el( 'h6', { className: 'sc_item_subtitle' }, props.attributes.subtitle )
				);
			}
			if ( props.attributes.title !== '' ) {
				children.push(
					el( 'h2', { className: 'sc_item_title' }, props.attributes.title )
				);
			}
			if ( props.attributes.description !== '' ) {
				children.push(
					el( 'div', { className: 'sc_item_description' }, props.attributes.description )
				);
			}
			return el('div', {
								id: props.attributes.id,
								className: props.className
							},
							children
					);
		},

		save( props ) {
			return el('p', null, 'Hello saved content.');
		},

	} );

})();
