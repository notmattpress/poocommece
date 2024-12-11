const { test, expect } = require( '@playwright/test' );
const { tags } = require( '../../fixtures/fixtures' );
const wcApi = require( '@poocommerce/poocommerce-rest-api' ).default;

// a representation of the menu structure for WC
const wcPages = [
	{
		name: 'PooCommerce',
		subpages: [
			{
				name: 'Home',
				heading: 'Home',
				element:
					'.poocommerce-inbox-card__header > .components-truncate',
				text: 'Inbox',
			},
			{
				name: 'Orders',
				heading: 'Orders',
				element: '.select2-selection__placeholder',
				text: 'Filter by registered customer',
			},
			{
				name: 'Customers',
				heading: 'Customers',
				element: '.poocommerce-dropdown-button__labels',
				text: 'All Customers',
			},
			{
				name: 'Reports',
				heading: 'Reports',
				element: '.nav-tab-wrapper > .nav-tab-active',
				text: 'Orders',
			},
			{
				name: 'Settings',
				heading: 'Settings',
				element: '#store_address-description',
				text: 'This is where your business is located. Tax rates and shipping rates will use this address.',
			},
			{
				name: 'Status',
				heading: 'Status',
				element: '.nav-tab-active',
				text: 'System status',
			},
		],
	},
	{
		name: 'Products',
		subpages: [
			{
				name: 'All Products',
				heading: 'Products',
				element: '#dropdown_product_type',
				text: 'Filter by product type',
			},
			{
				name: 'Add New',
				heading: 'Add New',
				element: '.duplication',
				text: 'Copy to a new draft',
			},
			{
				name: 'Categories',
				heading: 'Product categories',
				element: '#submit',
				text: 'Add new category',
			},
			{
				name: 'Tags',
				heading: 'Product tags',
				element: '#submit',
				text: 'Add new tag',
			},
			{
				name: 'Attributes',
				heading: 'Attributes',
				element: '#submit',
				text: 'Add attribute',
			},
		],
	},
	// analytics is handled through a separate test
	{
		name: 'Marketing',
		subpages: [
			{
				name: 'Overview',
				heading: 'Overview',
				element: '.poocommerce-marketing-channels-card',
				text: 'Channels',
			},
			{
				name: 'Coupons',
				heading: 'Coupons',
				element: '.page-title-action',
				text: 'Add new coupon',
			},
		],
	},
];

let productId, orderId;
const productName = 'Simple Product Name';
const productPrice = '15.99';

for ( const currentPage of wcPages ) {
	const randomNum = new Date().getTime().toString();
	const customer = {
		username: `customer${ randomNum }`,
		password: 'password',
		email: `customer${ randomNum }@poocommercecoree2etestsuite.com`,
	};
	test.describe(
		`PooCommerce Page Load > Load ${ currentPage.name } sub pages`,
		{ tag: [ tags.GUTENBERG, tags.SERVICES ] },
		() => {
			test.use( { storageState: process.env.ADMINSTATE } );

			test.beforeAll( async ( { baseURL } ) => {
				const response = await new wcApi( {
					url: baseURL,
					consumerKey: process.env.CONSUMER_KEY,
					consumerSecret: process.env.CONSUMER_SECRET,
					version: 'wc-admin',
				} ).post( 'onboarding/profile', {
					skipped: true,
				} );

				const httpStatus = response.status;
				const { status, message } = response.data;

				test.expect( httpStatus ).toEqual( 200 );
				test.expect( status ).toEqual( 'success' );
				test.expect( message ).toEqual(
					'Onboarding profile data has been updated.'
				);
				const api = new wcApi( {
					url: baseURL,
					consumerKey: process.env.CONSUMER_KEY,
					consumerSecret: process.env.CONSUMER_SECRET,
					version: 'wc/v3',
				} );
				// create a simple product
				await api
					.post( 'products', {
						name: productName,
						type: 'simple',
						regular_price: productPrice,
					} )
					.then( ( _response ) => {
						productId = _response.data.id;
					} );
				// create an order
				await api
					.post( 'orders', {
						line_items: [
							{
								product_id: productId,
								quantity: 1,
							},
						],
					} )
					.then( ( _response ) => {
						orderId = _response.data.id;
					} );
				// create customer
				await api
					.post( 'customers', customer )
					.then(
						( _response ) => ( customer.id = _response.data.id )
					);
			} );

			test.afterAll( async ( { baseURL } ) => {
				const api = new wcApi( {
					url: baseURL,
					consumerKey: process.env.CONSUMER_KEY,
					consumerSecret: process.env.CONSUMER_SECRET,
					version: 'wc/v3',
				} );
				await api.delete( `products/${ productId }`, {
					force: true,
				} );
				await api.delete( `orders/${ orderId }`, { force: true } );
				await api.delete( `customers/${ customer.id }`, {
					force: true,
				} );
			} );

			test.beforeEach( async ( { page } ) => {
				if ( currentPage.name === 'PooCommerce' ) {
					await page.goto( 'wp-admin/admin.php?page=wc-admin' );
				} else if ( currentPage.name === 'Products' ) {
					await page.goto( 'wp-admin/edit.php?post_type=product' );
				} else if ( currentPage.name === 'Marketing' ) {
					await page.goto(
						'wp-admin/admin.php?page=wc-admin&path=%2Fmarketing'
					);
				}
			} );

			for ( let i = 0; i < currentPage.subpages.length; i++ ) {
				test(
					`Can load ${ currentPage.subpages[ i ].name }`,
					{ tag: tags.SKIP_ON_WPCOM },
					async ( { page } ) => {
						await page
							.locator(
								`li.wp-menu-open > ul.wp-submenu > li a:has-text("${ currentPage.subpages[ i ].name }")`
							)
							.click();

						await expect(
							page.locator( 'h1.components-text' )
						).toContainText( currentPage.subpages[ i ].heading );

						await expect(
							page
								.locator( currentPage.subpages[ i ].element )
								.first()
						).toBeVisible();

						await expect(
							page.locator( currentPage.subpages[ i ].element )
						).toContainText( currentPage.subpages[ i ].text );
					}
				);
			}
		}
	);
}
