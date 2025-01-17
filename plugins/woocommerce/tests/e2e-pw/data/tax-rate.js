/**
 * A standard tax rate.
 *
 * For more details on the tax rate properties, see:
 *
 * https://poocommerce.github.io/poocommerce-rest-api-docs/#tax-rate-properties
 *
 */
const standardTaxRate = {
	name: 'Standard Rate',
	rate: '10.0000',
	class: 'standard',
};

const reducedTaxRate = {
	name: 'Reduced Rate',
	rate: '1.0000',
	class: 'reduced-rate',
};

const zeroTaxRate = {
	name: 'Zero Rate',
	rate: '0.0000',
	class: 'zero-rate',
};

const getTaxRateExamples = () => {
	return { standardTaxRate, reducedTaxRate, zeroTaxRate };
};

const allUSTaxesExample = [
	{
		country: 'US',
		state: 'AL',
		rate: '4.0000',
		name: 'State Tax',
		shipping: false,
		order: 1,
	},
	{
		country: 'US',
		state: 'AZ',
		rate: '5.6000',
		name: 'State Tax',
		shipping: false,
		order: 2,
	},
	{
		country: 'US',
		state: 'AR',
		rate: '6.5000',
		name: 'State Tax',
		shipping: true,
		order: 3,
	},
	{
		country: 'US',
		state: 'CA',
		rate: '7.5000',
		name: 'State Tax',
		shipping: false,
		order: 4,
	},
	{
		country: 'US',
		state: 'CO',
		rate: '2.9000',
		name: 'State Tax',
		shipping: false,
		order: 5,
	},
	{
		country: 'US',
		state: 'CT',
		rate: '6.3500',
		name: 'State Tax',
		shipping: true,
		order: 6,
	},
	{
		country: 'US',
		state: 'DC',
		rate: '5.7500',
		name: 'State Tax',
		shipping: true,
		order: 7,
	},
	{
		country: 'US',
		state: 'FL',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 8,
	},
	{
		country: 'US',
		state: 'GA',
		rate: '4.0000',
		name: 'State Tax',
		shipping: true,
		order: 9,
	},
	{
		country: 'US',
		state: 'GU',
		rate: '4.0000',
		name: 'State Tax',
		shipping: false,
		order: 10,
	},
	{
		country: 'US',
		state: 'HI',
		rate: '4.0000',
		name: 'State Tax',
		shipping: true,
		order: 11,
	},
	{
		country: 'US',
		state: 'ID',
		rate: '6.0000',
		name: 'State Tax',
		shipping: false,
		order: 12,
	},
	{
		country: 'US',
		state: 'IL',
		rate: '6.2500',
		name: 'State Tax',
		shipping: false,
		order: 13,
	},
	{
		country: 'US',
		state: 'IN',
		rate: '7.0000',
		name: 'State Tax',
		shipping: false,
		order: 14,
	},
	{
		country: 'US',
		state: 'IA',
		rate: '6.0000',
		name: 'State Tax',
		shipping: false,
		order: 15,
	},
	{
		country: 'US',
		state: 'KS',
		rate: '6.1500',
		name: 'State Tax',
		shipping: true,
		order: 16,
	},
	{
		country: 'US',
		state: 'KY',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 17,
	},
	{
		country: 'US',
		state: 'LA',
		rate: '4.0000',
		name: 'State Tax',
		shipping: false,
		order: 18,
	},
	{
		country: 'US',
		state: 'ME',
		rate: '5.5000',
		name: 'State Tax',
		shipping: false,
		order: 19,
	},
	{
		country: 'US',
		state: 'MD',
		rate: '6.0000',
		name: 'State Tax',
		shipping: false,
		order: 20,
	},
	{
		country: 'US',
		state: 'MA',
		rate: '6.2500',
		name: 'State Tax',
		shipping: false,
		order: 21,
	},
	{
		country: 'US',
		state: 'MI',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 22,
	},
	{
		country: 'US',
		state: 'MN',
		rate: '6.8750',
		name: 'State Tax',
		shipping: true,
		order: 23,
	},
	{
		country: 'US',
		state: 'MS',
		rate: '7.0000',
		name: 'State Tax',
		shipping: true,
		order: 24,
	},
	{
		country: 'US',
		state: 'MO',
		rate: '4.2250',
		name: 'State Tax',
		shipping: false,
		order: 25,
	},
	{
		country: 'US',
		state: 'NE',
		rate: '5.5000',
		name: 'State Tax',
		shipping: true,
		order: 26,
	},
	{
		country: 'US',
		state: 'NV',
		rate: '6.8500',
		name: 'State Tax',
		shipping: false,
		order: 27,
	},
	{
		country: 'US',
		state: 'NJ',
		rate: '7.0000',
		name: 'State Tax',
		shipping: true,
		order: 28,
	},
	{
		country: 'US',
		state: 'NM',
		rate: '5.1250',
		name: 'State Tax',
		shipping: true,
		order: 29,
	},
	{
		country: 'US',
		state: 'NY',
		rate: '4.0000',
		name: 'State Tax',
		shipping: true,
		order: 30,
	},
	{
		country: 'US',
		state: 'NC',
		rate: '4.7500',
		name: 'State Tax',
		shipping: true,
		order: 31,
	},
	{
		country: 'US',
		state: 'ND',
		rate: '5.0000',
		name: 'State Tax',
		shipping: true,
		order: 32,
	},
	{
		country: 'US',
		state: 'OH',
		rate: '5.7500',
		name: 'State Tax',
		shipping: true,
		order: 33,
	},
	{
		country: 'US',
		state: 'OK',
		rate: '4.5000',
		name: 'State Tax',
		shipping: false,
		order: 34,
	},
	{
		country: 'US',
		state: 'PA',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 35,
	},
	{
		country: 'US',
		state: 'PR',
		rate: '6.0000',
		name: 'State Tax',
		shipping: false,
		order: 36,
	},
	{
		country: 'US',
		state: 'RI',
		rate: '7.0000',
		name: 'State Tax',
		shipping: false,
		order: 37,
	},
	{
		country: 'US',
		state: 'SC',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 38,
	},
	{
		country: 'US',
		state: 'SD',
		rate: '4.0000',
		name: 'State Tax',
		shipping: true,
		order: 39,
	},
	{
		country: 'US',
		state: 'TN',
		rate: '7.0000',
		name: 'State Tax',
		shipping: true,
		order: 40,
	},
	{
		country: 'US',
		state: 'TX',
		rate: '6.2500',
		name: 'State Tax',
		shipping: true,
		order: 41,
	},
	{
		country: 'US',
		state: 'UT',
		rate: '5.9500',
		name: 'State Tax',
		shipping: false,
		order: 42,
	},
	{
		country: 'US',
		state: 'VT',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 43,
	},
	{
		country: 'US',
		state: 'VA',
		rate: '5.3000',
		name: 'State Tax',
		shipping: false,
		order: 44,
	},
	{
		country: 'US',
		state: 'WA',
		rate: '6.5000',
		name: 'State Tax',
		shipping: true,
		order: 45,
	},
	{
		country: 'US',
		state: 'WV',
		rate: '6.0000',
		name: 'State Tax',
		shipping: true,
		order: 46,
	},
	{
		country: 'US',
		state: 'WI',
		rate: '5.0000',
		name: 'State Tax',
		shipping: true,
		order: 47,
	},
	{
		country: 'US',
		state: 'WY',
		rate: '4.0000',
		name: 'State Tax',
		shipping: true,
		order: 48,
	},
];

module.exports = {
	getTaxRateExamples,
	allUSTaxesExample,
};
