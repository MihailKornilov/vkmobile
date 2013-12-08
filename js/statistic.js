Highcharts.setOptions({
	lang: {
		months: ['������', '�������', '����', '������', '���', '����', '����', '������', '��������', '�������', '������', '�������']
	}
});
$('#statistic').highcharts('StockChart', {
	chart: {
		zoomType: 'x',
		type: 'spline'
	},

	rangeSelector: {
		enabled: false
	},
	title: {
		text: '����� ������� �� �������'
	},
	legend: {
		enabled: true
	},
	series: [
		{
			name: '������',
			color:'#2A2',
			data: statPrihod,
			tooltip: {
				valueDecimals: 0
			}
		},
		{
			name: '������',
			color:'#922',
			data: statRashod,
			tooltip: {
				valueDecimals: 0
			}
		}
	]
});

$(document).ready(function() {
	if(VIEWER_ID != 982006 || DOMAIN != 'vkmobile')
		return;
	$('#statistic').after('<div id="stamina"></div>');
	var data = [
		['2013-06-18', 3662, 107, 11.2, 1],

		['2013-06-19', 3792, 104, 11.3],

		['2013-06-20', 4275, 104, 8.6],

		['2013-06-21', 4394, 116, 7.3],
		['2013-06-21', 3791, 122, 8.6],
		['2013-06-21', 4111, 123, 9.4],
		['2013-06-21', 3983, 126, 7.6],
		['2013-06-21', 4511, 128, 7.9],
		['2013-06-21', 4645, 135, 8.4],
		['2013-06-21', 3790, 122, 8.1],
		['2013-06-21', 4434, 112, 6.1],

		['2013-06-22', 4893, 125, 6.8],
		['2013-06-22', 4087, 121, 6.7],
		['2013-06-22', 3981, 128, 8.5],
		['2013-06-22', 3639, 124, 5.9],
		['2013-06-22', 3868, 124, 6.6],
		['2013-06-22', 3884, 130, 7.2],

		['2013-06-23', 3755, 122, 4.9],
		['2013-06-23', 3787, 138, 7.7],
		['2013-06-23', 4091, 131, 6.4],
		['2013-06-23', 4185, 143, 7.4],
		['2013-06-23', 4523, 136, 6.1],
		['2013-06-23', 3551, 136, 7.6],
		['2013-06-23', 4216, 129, 7.7],
		['2013-06-23', 3944, 139, 7.1],

		['2013-06-24', 4292, 137, 4.6],
		['2013-06-24', 4345, 156, 4.9],
		['2013-06-24', 4040, 142, 5.5],
		['2013-06-24', 4040, 142, 5.0],
		['2013-06-24', 4464, 142, 5.5],
		['2013-06-24', 4179, 139, 6.5],

		['2013-06-25', 3947, 158, 5.4],
		['2013-06-25', 3593, 144, 4.1],
		['2013-06-25', 3132, 139, 4.6],
		['2013-06-25', 8063, 149, 5.8],
		['2013-06-25', 4470, 153, 6.1],
		['2013-06-25', 4380, 166, 6.8],
		['2013-06-25', 3797, 160, 6.8],
		['2013-06-25', 4171, 159, 7.0],
		['2013-06-25', 3644, 124, 3.6],

		['2013-06-26', 4108, 153, 4.7],
		['2013-06-26', 4670, 165, 5.0],
		['2013-06-26', 4753, 155, 5.9],
		['2013-06-26', 4918, 169, 5.4],
		['2013-06-26', 4272, 167, 4.7],
		['2013-06-26', 3338, 156, 6.7],

		['2013-06-27', 4407, 143, 5.6],
		['2013-06-27', 4646, 179, 4.5],
		['2013-06-27', 4826, 180, 4.8],
		['2013-06-27', 4206, 165, 5.1],
		['2013-06-27', 3928, 164, 4.7],
		['2013-06-27', 4381, 148, 7.1],

		['2013-06-28', 4776, 173, 5.8],
		['2013-06-28', 3907, 171, 4.7],
		['2013-06-28', 3990, 178, 5.7],
		['2013-06-28', 3289, 173, 6.6],
		['2013-06-28', 4856, 175, 5.8],
		['2013-06-28', 4673, 183, 5.4],
		['2013-06-28', 4493, 173, 5.8],
		['2013-06-28', 3925, 168, 6.3],

		['2013-06-29', 4658, 173, 7.2],
		['2013-06-29', 4912, 173, 7.1],
		['2013-06-29', 3658, 169, 2.9],
		['2013-06-29', 5534, 166, 5.1],
		['2013-06-29', 7133, 185, 6.5],
		['2013-06-29', 6184, 171, 8.8],
		['2013-06-29', 6387, 170, 5.4],
		['2013-06-29', 5149, 170, 4.5],

		['2013-06-30', 4912, 201, 3.3],
		['2013-06-30', 5011, 184, 4.3],
		['2013-06-30', 4580, 194, 3.4],
		['2013-06-30', 6293, 188, 5.4],
		['2013-06-30', 3627, 157, 6.4],

		['2013-07-01', 8047, 191, 3.7],

		['2013-07-02', 7263, 168, 5.5],

		['2013-07-03', 6067, 183, 5.0],

		['2013-07-05', 4299, 192, 5.0],
		['2013-07-05', 3941, 194, 6.4],
		['2013-07-05', 4419, 213, 5.4],
		['2013-07-05', 4447, 208, 6.2],
		['2013-07-05', 5930, 191, 6.6],
		['2013-07-05', 3409, 210, 4.3],
		['2013-07-05', 11607, 210, 5.3],
		['2013-07-05', 6016, 189, 3.7],

		['2013-07-07', 4971, 204, 4.2],
		['2013-07-07', 4048, 214, 5.0],
		['2013-07-07', 4780, 214, 4.6],
		['2013-07-07', 4898, 218, 5.8],
		['2013-07-07', 4962, 216, 3.8],
		['2013-07-07', 5788, 178, 1.9],
		['2013-07-07', 3896, 206, 4.3],
		['2013-07-07', 8896, 201, 4.9],

		['2013-07-08', 8453, 208, 5.0, 2],
		['2013-07-08', 7541, 200, 4.7],
		['2013-07-08', 7957, 199, 4.1],
		['2013-07-08', 8731, 197, 5.4],

		['2013-07-09', 8111, 202, 3.6],
		['2013-07-09', 10435, 187, 3.8],

		['2013-07-10', 7376, 201, 4.7],
		['2013-07-10', 8859, 217, 6.0],
		['2013-07-10', 6000, 208, 3.1],

		['2013-07-11', 5166, 216, 4.0],
		['2013-07-11', 6957, 207, 4.4],
		['2013-07-11', 8405, 206, 5.1],
		['2013-07-11', 7306, 200, 4.4],

		['2013-07-12', 8877, 193, 4.8],
		['2013-07-12', 5557, 198, 2.9],
		['2013-07-12', 7334, 200, 2.6],
		['2013-07-12', 9078, 187, 2.3],
		['2013-07-12', 8911, 190, 5.9],

		['2013-07-13', 3644, 218, 2.9],
		['2013-07-13', 5581, 222, 3.5],
		['2013-07-13', 4834, 192, 2.1],
		['2013-07-13', 4240, 198, 3.3],

		['2013-07-14', 4532, 197, 2.8],
		['2013-07-14', 4873, 201, 2.9],
		['2013-07-14', 3860, 205, 2.1],
		['2013-07-14', 4774, 219, 3.9],
		['2013-07-14', 4267, 222, 3.3],
		['2013-07-14', 4315, 211, 3.4],
		['2013-07-14', 4450, 214, 4.6],
		['2013-07-14', 4296, 208, 4.5],
		['2013-07-14', 4203, 198, 2.3],
		['2013-07-14', 4083, 195, 2.2],
		['2013-07-14', 5232, 194, 3.4],

		['2013-07-15', 4792, 207, 3.1],
		['2013-07-15', 5865, 209, 4.2],
		['2013-07-15', 5283, 215, 4.1],
		['2013-07-15', 4357, 208, 4.6],

		['2013-07-16', 3657, 204, 3.6],

		['2013-07-17', 4739, 200, 3.6],
		['2013-07-17', 3196, 211, 4.0],
		['2013-07-17', 4738, 219, 5.3],
		['2013-07-17', 4782, 210, 4.1],

		['2013-07-18', 4178, 220, 3.9],
		['2013-07-18', 6151, 218, 3.8],
		['2013-07-18', 5665, 236, 4.0],
		['2013-07-18', 6514, 229, 4.3],
		['2013-07-18', 4548, 227, 4.0],

		['2013-07-19', 4135, 235, 4.5],
		['2013-07-19', 4509, 234, 4.0],
		['2013-07-19', 4146, 246, 4.4],
		['2013-07-19', 4509, 217, 4.2],

		['2013-07-20', 4712, 214, 4.4],
		['2013-07-20', 3990, 219, 4.6],
		['2013-07-20', 4565, 225, 1.8],
		['2013-07-20', 4520, 226, 2.4],
		['2013-07-20', 4750, 222, 4.2],
		['2013-07-20', 4527, 222, 3.4],
		['2013-07-20', 4494, 225, 4.9],
		['2013-07-20', 4541, 225, 4.7],

		['2013-07-21', 4284, 217, 3.8],
		['2013-07-21', 4190, 219, 3.8],
		['2013-07-21', 4048, 191, 1.6],
		['2013-07-21', 5383, 213, 3.4],
		['2013-07-21', 4985, 207, 4.8],
		['2013-07-21', 5503, 221, 3.8],
		['2013-07-21', 6549, 217, 4.6],
		['2013-07-21', 4385, 215, 5.0],
		['2013-07-21', 4136, 210, 5.0],
		['2013-07-21', 4485, 204, 4.8],

		['2013-07-22', 4628, 205, 4.0],
		['2013-07-22', 4457, 218, 3.8],
		['2013-07-22', 4836, 235, 3.4],
		['2013-07-22', 8071, 226, 4.3],

		['2013-07-23', 4647, 222, 3.9],
		['2013-07-23', 3074, 217, 4.7],
		['2013-07-23', 4997, 229, 3.7],
		['2013-07-23', 6534, 225, 3.5],

		['2013-07-24', 4309, 211, 4.6],
		['2013-07-24', 4177, 220, 3.8],
		['2013-07-24', 5225, 226, 4.0],

		['2013-07-25', 4640, 203, 4.3],

		['2013-07-26', 5282, 223, 4.7],

		['2013-07-27', 5401, 242, 3.5],
		['2013-07-27', 3802, 221, 4.6],
		['2013-07-27', 3874, 227, 3.4],
		['2013-07-27', 4133, 246, 3.7],
		['2013-07-27', 4774, 248, 3.9],
		['2013-07-27', 4188, 236, 4.3],
		['2013-07-27', 4300, 251, 4.0],
		['2013-07-27', 3542 ,243, 5.3],

		['2013-07-31', 5475, 226, 3.7, 3],
		['2013-07-31', 6559, 234, 3.7],
		['2013-07-31', 6124, 226, 4.7],

		['2013-08-01', 5984, 237, 3.4],
		['2013-08-01', 3225, 233, 3.5],
		['2013-08-01', 3139, 247, 4.9],
		['2013-08-01', 4864, 236, 4.8],

		['2013-08-02', 4052, 235, 4.7],
		['2013-08-02', 6391, 231, 3.8],

		['2013-08-03', 4169, 207, 3.5],
		['2013-08-03', 5184, 206, 6.1],
		['2013-08-03', 3430, 197, 4.6],
		['2013-08-03', 3322 ,189, 5.8],

		['2013-08-04', 3545, 237, 3.3],
		['2013-08-04', 3839, 232, 5.0],

		['2013-08-05', 3690, 235, 4.0],
		['2013-08-05', 4032, 247, 4.5],

		['2013-08-06', 4603, 244, 3.9],
		['2013-08-06', 3316, 243, 4.0],
		['2013-08-06', 4955, 208, 5.0],

		['2013-08-07', 5092, 237, 4.1],

		['2013-08-08', 4207, 257, 4.1],
		['2013-08-08', 3900, 233, 3.3],
		['2013-08-08', 4184, 250, 3.1],
		['2013-08-08', 3734, 249, 4.3],
		['2013-08-08', 5973, 250, 4.8],
		['2013-08-08', 5119, 247, 3.7],
		['2013-08-08', 4138, 255, 3.7],

		['2013-08-09', 4593, 243, 4.2],
		['2013-08-09', 4057, 250, 3.8],
		['2013-08-09', 4389, 243, 3.4],

		['2013-08-11', 2820, 235, 3.4],
		['2013-08-11', 4319, 239, 3.7],
		['2013-08-11', 4869, 259, 3.4],
		['2013-08-11', 4853, 270, 4.0],

		['2013-08-12', 4862, 267, 3.5],
		['2013-08-12', 5519, 260, 4.1],
		['2013-08-12', 4145, 244, 4.2],
		['2013-08-12', 4366, 258, 3.1],
		['2013-08-12', 4661, 259, 3.3],
		['2013-08-12', 3027, 244, 4.2],

		['2013-08-13', 3416, 243, 3.8],
		['2013-08-13', 4855, 232, 4.5],

		['2013-08-14', 4109, 233, 2.9],
		['2013-08-14', 4532, 247, 3.9],
		['2013-08-14', 4479, 255, 3.9],
		['2013-08-14', 3404, 265, 4.4],
		['2013-08-14', 4805, 249, 4.2],

		['2013-08-15', 4905, 230, 3.9],
		['2013-08-15', 5218, 262, 3.6],
		['2013-08-15', 4205, 244, 4.5],
		['2013-08-15', 4035, 255, 3.4],

		['2013-08-16', 3194, 239, 4.1],
		['2013-08-16', 4287, 256, 4.1],
		['2013-08-16', 4425, 268, 3.8],
		['2013-08-16', 5044, 268, 4.3],
		['2013-08-16', 4275, 271, 4.2],
		['2013-08-16', 5107, 259, 3.3],
		['2013-08-16', 4159, 253, 3.5],

		['2013-08-17', 3543, 186, 5.7],
		['2013-08-17', 3367, 239, 4.2],
		['2013-08-17', 4342, 232, 4.6],

		['2013-08-18', 4237, 234, 3.6],
		['2013-08-18', 4579, 247, 3.6],
		['2013-08-18', 4624, 247, 4.2],
		['2013-08-18', 4746, 253, 5.1],
		['2013-08-18', 5359, 237, 4.6],
		['2013-08-18', 5172, 246, 4.1],
		['2013-08-18', 3307, 253, 3.8],
		['2013-08-18', 4342, 220, 5.6],
		['2013-08-18', 4438, 214, 5.5],
		['2013-08-18', 7669, 242, 4.3],
		['2013-08-18', 5302, 241, 4.1],
		['2013-08-18', 5532, 248, 4.4],
		['2013-08-18', 5060, 239, 5.6],
		['2013-08-18', 7156, 248, 4.4],

		['2013-08-19', 3910, 228, 4.5, 4],
		['2013-08-19', 4065, 242, 4.4],
		['2013-08-19', 4624, 236, 4.7],
		['2013-08-19', 4608, 223, 4.3],
		['2013-08-19', 4501, 224, 4.8],
		['2013-08-19', 3603, 219, 4.1],

		['2013-08-20', 3621, 234, 4.3],
		['2013-08-20', 4014, 237, 3.6],

		['2013-08-21', 4294, 242, 4.1],
		['2013-08-21', 3844, 237, 4.4],
		['2013-08-21', 4437, 248, 4.4],
		['2013-08-21', 4096, 247, 3.9],

		['2013-08-22', 4377, 248, 3.8],
		['2013-08-22', 3868, 254, 3.9],
		['2013-08-22', 3073, 255, 3.4],

		['2013-08-23', 5495, 249, 3.6],
		['2013-08-23', 4406, 263, 2.5],

		['2013-08-24', 3896, 229, 3.0],
		['2013-08-24', 4527, 242, 3.4],
		['2013-08-24', 3894, 244, 2.9],
		['2013-08-24', 4326, 255, 3.2],

		['2013-08-25', 4108, 273, 4.6],
		['2013-08-25', 4431, 263, 6.1],

		['2013-08-26', 5734, 253, 4.5],
		['2013-08-26', 7017, 251, 3.8],
		['2013-08-26', 4485, 267, 3.4],
		['2013-08-26', 5008, 271, 3.2],
		['2013-08-26', 5077, 257, 3.8],

		['2013-08-27', 5831, 257, 2.2],
		['2013-08-27', 3808, 253, 3.1],

		['2013-08-28', 4400, 243, 2.3],
		['2013-08-28', 4081, 259, 2.3],
		['2013-08-28', 3410, 264, 3.0],
		['2013-08-28', 5036, 263, 2.9],
		['2013-08-28', 4257, 261, 4.0],
		['2013-08-28', 4452, 242, 3.3],

		['2013-08-30', 4176, 270, 3.7],

		['2013-08-31', 3977, 264, 3.5],
		['2013-08-31', 4192, 266, 4.0],
		['2013-08-31', 4048, 253, 4.2],
		['2013-08-31', 3500, 245, 3.7],

		['2013-09-01', 4682, 250, 3.3],
		['2013-09-01', 4077, 255, 4.3],

		['2013-09-02', 4518, 277, 3.7],
		['2013-09-02', 4121, 276, 3.7],
		['2013-09-02', 4221, 273, 3.3],

		['2013-09-03', 4740, 270, 3.1],
		['2013-09-03', 5808, 258, 3.2],

		['2013-09-04', 3483, 267, 4.0],
		['2013-09-04', 4161, 270, 4.0],
		['2013-09-04', 4066, 266, 4.2],
		['2013-09-04', 4636, 263, 3.4],
		['2013-09-04', 4331, 243, 3.5],
		['2013-09-04', 11243, 237, 5.0],

		['2013-09-07', 6184, 271, 3.2],
		['2013-09-07', 4673, 274, 3.8],
		['2013-09-07', 4546, 253, 4.1],
		['2013-09-07', 4357, 260, 3.3],
		['2013-09-07', 4415, 247, 3.2],
		['2013-09-07', 5637, 267, 3.6],
		['2013-09-07', 5535, 252, 4.0],
		['2013-09-07', 5877, 246, 3.5],

		['2013-09-08', 4742, 285, 3.9],
		['2013-09-08', 3767, 281, 3.3],
		['2013-09-08', 4823, 290, 3.0],
		['2013-09-08', 4101, 268, 2.7],
		['2013-09-08', 4184, 276, 2.8],
		['2013-09-08', 4118, 268, 3.4],
		['2013-09-08', 4758, 274, 3.7],
		['2013-09-08', 3927, 271, 3.6],

		['2013-09-09', 4391, 288, 3.3],
		['2013-09-09', 4281, 268, 4.1],

		['2013-09-10', 3969, 279, 3.2],
		['2013-09-10', 4373, 280, 3.7],
		['2013-09-10', 9429, 265, 4.8],
		['2013-09-10', 4959, 277, 4.7],
		['2013-09-10', 5100, 269, 3.2],
		['2013-09-10', 5659, 281, 3.7],

		['2013-09-11', 6435, 267, 2.9],
		['2013-09-11', 4559, 266, 2.9],
		['2013-09-11', 3901, 265, 2.7],
		['2013-09-11', 4677, 251, 4.2],

		['2013-09-12', 4020, 280, 2.9],
		['2013-09-12', 4745, 268, 4.4],
		['2013-09-12', 5099, 278, 4.1],

		['2013-09-13', 6345, 270, 2.9],
		['2013-09-13', 6432, 282, 3.2],
		['2013-09-13', 4816, 274, 4.2],
		['2013-09-13', 3866, 278, 3.8],
		['2013-09-13', 4176, 295, 4.6],

		['2013-09-14', 3709, 284, 4.7],
		['2013-09-14', 4672, 287, 4.7],
		['2013-09-14', 4393, 279, 4.8],
		['2013-09-14', 4731, 270, 4.7],
		['2013-09-14', 4174, 275, 4.6],
		['2013-09-14', 4226, 276, 4.1],
		['2013-09-14', 3534, 277, 4.1],
		['2013-09-14', 5169, 268, 3.3],

		['2013-09-15', 3774, 252, 3.8],
		['2013-09-15', 4363, 258, 3.2],
		['2013-09-15', 4603, 274, 4.2],
		['2013-09-15', 4458, 268, 4.0],
		['2013-09-15', 4209, 259, 4.3],
		['2013-09-15', 4291, 266, 5.0],

		['2013-09-16', 9154, 276, 4.2],
		['2013-09-16', 7825, 269, 4.8],
		['2013-09-16', 9613, 264, 4.9],
		['2013-09-16', 8319, 267, 4.6],
		['2013-09-16', 4087, 262, 4.4],

		['2013-09-17', 6928, 254, 3.6, 5],

		['2013-09-18', 8683, 273, 4.4],
		['2013-09-18', 9992, 282, 4.3],
		['2013-09-18', 8188, 277, 3.5],
		['2013-09-18', 8229, 279, 4.2],
		['2013-09-18', 8910, 257, 4.3],

		['2013-09-19', 9384, 268, 4.1],
		['2013-09-19', 5344, 271, 3.9],
		['2013-09-19', 5948, 258, 4.9],
		['2013-09-19', 5784, 258, 4.2],
		['2013-09-19', 7455, 253, 4.4],
		['2013-09-19', 5547, 250, 4.9],

		['2013-09-20', 5493, 273, 4.2],
		['2013-09-20', 5809, 273, 4.1],
		['2013-09-20', 5529, 276, 4.0],
		['2013-09-20', 6145, 264, 4.1],
		['2013-09-20', 7024, 271, 4.2],
		['2013-09-20', 5448, 266, 4.9],
		['2013-09-20', 4835, 273, 3.9],
		['2013-09-20', 5476, 246, 4.2],
		['2013-09-20', 7470, 237, 5.1],

		['2013-09-21', 5376, 279, 4.4],
		['2013-09-21', 5991, 274, 5.0],
		['2013-09-21', 5644, 266, 4.4],
		['2013-09-21', 5791, 269, 4.0],
		['2013-09-21', 5257, 258, 4.5],
		['2013-09-21', 4900, 255, 4.5],
		['2013-09-21', 5267, 237, 5.6],
		['2013-09-21', 4435, 231, 5.6],

		['2013-09-22', 5467, 265, 4.6],
		['2013-09-22', 5275, 276, 4.2],
		['2013-09-22', 5668, 263, 4.6],
		['2013-09-22', 6310, 273, 4.2],
		['2013-09-22', 2577, 266, 3.9],
		['2013-09-22', 5661, 276, 3.2],
		['2013-09-22', 6648, 270, 4.6],
		['2013-09-22', 4526, 255, 5.1],
		['2013-09-22', 5860, 262, 5.5],
		['2013-09-22', 5381, 229, 4.7],
		['2013-09-22', 5157, 209, 3.1],
		['2013-09-22', 5180, 202, 2.1],
		['2013-09-22', 5148, 199, 3.2],

		['2013-09-23', 5911, 210, 2.5],
		['2013-09-23', 4259, 241, 2.4],
		['2013-09-23', 7188, 209, 4.4],
		['2013-09-23', 5161, 266, 2.3],
		['2013-09-23', 6620, 279, 2.8],
		['2013-09-23', 5673, 272, 3.5],
		['2013-09-23', 7137, 266, 4.5],
		['2013-09-23', 5249, 244, 4.5],
		['2013-09-23', 5983, 224, 3.5],
		['2013-09-23', 6187, 261, 4.2],
		['2013-09-23', 6608, 271, 4.3],
		['2013-09-23', 4199, 262, 4.6],
		['2013-09-23', 3155, 254, 5.4],

		['2013-09-24', 6271, 251, 3.7],
		['2013-09-24', 5395, 254, 3.8],
		['2013-09-24', 5526, 259, 4.4],
		['2013-09-24', 5815, 256, 4.7],
		['2013-09-24', 4822, 251, 4.7],
		['2013-09-24', 3632, 252, 4.8],
		['2013-09-24', 6160, 246, 4.7],
		['2013-09-24', 6047, 229, 2.2],

		['2013-09-26', 5315, 265, 3.9],
		['2013-09-26', 7143, 267, 3.6],

		['2013-09-27', 6076, 277, 4.0],
		['2013-09-27', 6740, 271, 4.4],
		['2013-09-27', 6498, 258, 4.6],

		['2013-10-02', 5246, 282, 3.5],
		['2013-10-02', 7440, 280, 3.9],
		['2013-10-02', 5177, 274, 3.6],

		['2013-10-03', 5244, 282, 3.4],
		['2013-10-03', 5439, 282, 3.5],
		['2013-10-03', 5835, 278, 3.2],
		['2013-10-03', 5221, 273, 4.4],
		['2013-10-03', 5428, 269, 4.1],

		['2013-10-04', 6201, 290, 3.7],
		['2013-10-04', 5673, 295, 3.6],
		['2013-10-04', 5786, 298, 3.9],
		['2013-10-04', 4881, 297, 3.1],
		['2013-10-04', 5007, 289, 3.7],
		['2013-10-04', 4514, 288, 3.9],

		['2013-10-05', 5644, 278, 3.4],
		['2013-10-05', 6674, 273, 3.4],
		['2013-10-05', 5309, 288, 3.2],
		['2013-10-05', 5523, 292, 3.2],
		['2013-10-05', 6352, 289, 3.7],
		['2013-10-05', 5416, 278, 4.1],

		['2013-10-06', 4952, 282, 3.0],
		['2013-10-06', 6180, 274, 3.1],
		['2013-10-06', 5532, 286, 2.8],
		['2013-10-06', 4727, 282, 4.1],
		['2013-10-06', 4641, 273, 3.8],
		['2013-10-06', 4362, 258, 4.2],
		['2013-10-06', 3064, 264, 3.0, 6],
		['2013-10-06', 5575, 262, 3.3],
		['2013-10-06', 7311, 255, 3.8],
		['2013-10-06', 7169, 251, 4.3],
		['2013-10-06', 7776, 258, 3.1],
		['2013-10-06', 6446, 252, 4.0],
		['2013-10-06', 3498, 237, 4.2],

		['2013-10-07', 5421, 254, 3.7],
		['2013-10-07', 5778, 246, 3.8],
		['2013-10-07', 4981, 260, 2.8],

		['2013-10-08', 2887, 283, 2.9],
		['2013-10-08', 5348, 287, 2.4],
		['2013-10-08', 5943, 281, 3.1],
		['2013-10-08', 6972, 285, 3.5],
		['2013-10-08', 5807, 278, 3.2],
		['2013-10-08', 6383, 275, 3.0],
		['2013-10-08', 4951, 278, 2.9],
		['2013-10-08', 5932, 285, 3.7],
		['2013-10-08', 6398, 272, 4.1],
		['2013-10-08', 5796, 282, 3.5],

		['2013-10-09', 5214, 289, 3.5],
		['2013-10-09', 6270, 279, 2.9],
		['2013-10-09', 5440, 279, 3.1],

		['2013-10-10', 6456, 279, 3.9],
		['2013-10-10', 5017, 279, 3.4],
		['2013-10-10', 5965, 287, 3.3],
		['2013-10-10', 5455, 279, 3.8],
		['2013-10-10', 5899, 295, 3.6],
		['2013-10-10', 5903, 291, 3.2],
		['2013-10-10', 3341, 278, 3.4],
		['2013-10-10', 4313, 276, 3.1],
		['2013-10-10', 4988, 287, 3.6],
		['2013-10-10', 5576, 283, 3.4],
		['2013-10-10', 6186, 284, 3.9],

		['2013-10-11', 6476, 271, 3.3],
		['2013-10-11', 5664, 264, 3.8],
		['2013-10-11', 5479, 280, 3.8],
		['2013-10-11', 5606, 277, 3.8],

		['2013-10-12', 5140, 291, 2.9],
		['2013-10-12', 5391, 286, 3.5],
		['2013-10-12', 6030, 272, 3.1],
		['2013-10-12', 5152, 259, 4.3],
		['2013-10-12', 5321, 272, 2.9],
		['2013-10-12', 2491, 279, 4.1],
		['2013-10-12', 4774, 284, 3.4],
		['2013-10-12', 5657, 273, 4.1],
		['2013-10-12', 5792, 280, 3.9],

		['2013-10-13', 5181, 283, 3.2],
		['2013-10-13', 5457, 294, 2.8],
		['2013-10-13', 6001, 291, 2.6],
		['2013-10-13', 5377, 284, 3.1],
		['2013-10-13', 6450, 287, 2.7],
		['2013-10-13', 5469, 269, 2.9],
		['2013-10-13', 5773, 286, 3.1],
		['2013-10-13', 6347, 285, 3.2],

		['2013-10-14', 6098, 290, 3.4],
		['2013-10-14', 4958, 282, 2.6],
		['2013-10-14', 5610, 279, 3.0],
		['2013-10-14', 5532, 282, 2.5],
		['2013-10-14', 5233, 275, 3.0],
		['2013-10-14', 5523, 288, 3.1],
		['2013-10-14', 8646, 278, 4.2],

		['2013-10-15', 5944, 285, 3.5],
		['2013-10-15', 5854, 281, 3.5],
		['2013-10-15', 5324, 277, 4.1],
		['2013-10-15', 5663, 281, 3.4],
		['2013-10-15', 5884, 289, 3.6],
		['2013-10-15', 5224, 277, 3.6],

		['2013-10-16', 6540, 288, 3.6],
		['2013-10-16', 5546, 286, 3.4],
		['2013-10-16', 5642, 269, 3.4],
		['2013-10-16', 5995, 262, 2.7],

		['2013-10-17', 5246, 278, 3.1],
		['2013-10-17', 6182, 298, 4.5],
		['2013-10-17', 6416, 292, 4.7],
		['2013-10-17', 5784, 273, 3.6],

		['2013-10-18', 6197, 284, 4.0],
		['2013-10-18', 5552, 305, 4.1],
		['2013-10-18', 6101, 298, 4.5],
		['2013-10-18', 5716, 286, 5.0],
		['2013-10-18', 6148, 285, 4.2],
		['2013-10-18', 6341, 287, 4.9],
		['2013-10-18', 5032, 275, 4.9],

		['2013-10-19', 6276, 278, 3.6],
		['2013-10-19', 5593, 285, 3.8],
		['2013-10-19', 10137, 280, 4.2],
		['2013-10-19', 10131, 268, 4.6],
		['2013-10-19', 10087, 274, 3.5],

		['2013-10-20', 10226, 281, 3.6],
		['2013-10-20', 10356, 291, 3.1],
		['2013-10-20', 10114, 281, 2.8],
		['2013-10-20', 10246, 279, 2.4],
		['2013-10-20', 10059, 273, 3.4],
		['2013-10-20', 10051, 275, 4.1],

		['2013-10-21', 10004, 284, 2.9],
		['2013-10-21', 10107, 296, 3.4],
		['2013-10-21', 10406, 290, 3.0],
		['2013-10-21', 10075, 275, 3.6],
		['2013-10-21', 9337, 272, 3.8],

		['2013-10-22', 7209, 289, 2.6, 7],
		['2013-10-22', 10298, 286, 3.5],
		['2013-10-22', 10071, 274, 3.7],
		['2013-10-22', 7034, 275, 4.6],

		['2013-10-23', 8162, 278, 4.3],
		['2013-10-23', 10125, 277, 4.3],

		['2013-10-24', 9853, 287, 4.0],
		['2013-10-24', 10635, 292, 4.6],
		['2013-10-24', 5551, 310, 3.0],
		['2013-10-24', 10004, 285, 5.1],

		['2013-10-25', 9948, 301, 3.4],
		['2013-10-25', 10121, 283, 4.4],
		['2013-10-25', 10109, 289, 3.6],
		['2013-10-25', 10098, 288, 3.8],

		['2013-10-26', 10193, 296, 4.2],
		['2013-10-26', 6741, 293, 4.2],

		['2013-10-27', 10341, 276, 4.3],
		['2013-10-27', 10805, 273, 4.1],
		['2013-10-27', 8144, 298, 4.3],
		['2013-10-27', 9354, 312, 4.6],
		['2013-10-27', 10791, 291, 4.3],

		['2013-10-28', 9657, 294, 3.3],
		['2013-10-28', 9799, 291, 3.3],
		['2013-10-28', 9862, 303, 3.6],

		['2013-10-29', 10077, 294, 3.7],

		['2013-10-30', 10097, 303, 3.5],
		['2013-10-30', 9343, 305, 3.3],
		['2013-10-30', 5916, 293, 3.8],

		['2013-10-31', 9341, 283, 3.8],
		['2013-10-31', 9886, 311, 3.8],

		['2013-11-02', 7813, 307, 2.8],
		['2013-11-02', 7211, 315, 3.0],

		['2013-11-03', 10056, 294, 3.5],
		['2013-11-03', 10140, 304, 3.7],

		['2013-11-04', 10630, 288, 3.3],

		['2013-11-05', 10343, 306, 3.4],

		['2013-11-06', 10181, 298, 3.7],
		['2013-11-06', 12971, 292, 3.8],
		['2013-11-06', 13186, 290, 3.9],

		['2013-11-07', 10945, 301, 3.6],
		['2013-11-07', 10238, 303, 3.6],
		['2013-11-07', 12743, 292, 3.8],

		['2013-11-08', 10198, 301, 3.8],
		['2013-11-08', 10395, 291, 4.2],
		['2013-11-08', 10131, 289, 4.2],

		['2013-11-09', 11055, 298, 3.7],
		['2013-11-09', 10201, 288, 3.6],

		['2013-11-10', 8808, 308, 5.0],
		['2013-11-10', 6925, 313, 3.7],
		['2013-11-10', 10044, 291, 4.7],

		['2013-11-11', 10574, 300, 3.7],
		['2013-11-11', 10384, 295, 3.7],
		['2013-11-11', 10365, 274, 5.0],

		['2013-11-12', 10984, 298, 3.4],
		['2013-11-12', 6791, 314, 2.9],

		['2013-11-14', 10060, 266, 5.0],

		['2013-11-15', 10076, 312, 3.2],
		['2013-11-15', 11989, 292, 3.3],

		['2013-11-16', 7976, 301, 3.2, 8],
		['2013-11-16', 10096, 294, 3.1],
		['2013-11-16', 6103, 294, 3.8],

		['2013-11-17', 10021, 309, 4.3],
		['2013-11-17', 10248, 290, 4.0],
		['2013-11-17', 9753, 281, 4.8],

		['2013-11-18', 5004, 295, 2.8],
		['2013-11-18', 10084, 308, 3.2],
		['2013-11-18', 9822, 281, 4.1],

		['2013-11-19', 7614, 296, 3.1],
		['2013-11-19', 10243, 308, 3.2],

		['2013-11-20', 10329, 301, 3.1],
		['2013-11-20', 4481, 306, 3.4],

		['2013-11-21', 9987, 303, 3.5],
		['2013-11-21', 10114, 302, 3.2],
		['2013-11-21', 4448, 238, 8.0],

		['2013-11-22', 4448, 313, 4.4],
		['2013-11-22', 9993, 298, 3.5],
		['2013-11-22', 10013, 295, 3.9],

		['2013-11-24', 8084, 307, 3.8],
		['2013-11-24', 8293, 305, 4.0],

		['2013-11-25', 9897, 303, 3.9],

		['2013-11-26', 9908, 310, 3.2],

		['2013-11-27', 10012, 313, 3.5],
		['2013-11-27', 10359, 314, 3.4],
		['2013-11-27', 12261, 294, 3.9],

		['2013-11-28', 10026, 287, 3.0],

		['2013-12-01', 9814, 310, 4.0],
		['2013-12-01', 8585, 305, 3.7],

		['2013-12-03', 10115, 294, 3.8],
		['2013-12-03', 6329, 296, 3.6],

		['2013-12-05', 9440, 320, 4.2],
		['2013-12-05', 9934, 318, 3.0],
		['2013-12-05', 7194, 314, 3.4],

		['2013-12-06', 10052, 301, 3.8],

		['2013-12-07', 10196, 311, 3.3]

	];
	var symbols = [],
		speed = [],
		errors = [],
		hour,
		curDay;
	for(var n = 0; n < data.length; n++) {
		var unit = data[n];
		if(curDay != unit[0]) {
			hour = 7;
			curDay = unit[0];
		}
		var day = (new Date(unit[0] + ' ' + (hour < 10 ? '0' : '') + hour + ':00:00')).getTime();
		symbols.push([day, unit[1]]);
		speed.push([day, unit[2]]);
		errors.push([day, unit[3]]);
		hour++;
	}
	$('#stamina').highcharts('StockChart', {
		chart: {
			zoomType: 'x',
			type: 'spline',
			height:600
		},

		rangeSelector: {
			enabled: false
		},
		title: {
			text: '�������� ������ ������ ������'
		},
		legend: {
			enabled: true
		},
		series: [{
			name: '�������� ��/���',
			color:'#22a',
			data:speed,
			tooltip: {
				valueDecimals: 0
			}
		},{
			name: '��������',
			color:'#2A2',
			data:symbols,
			tooltip: {
				valueDecimals: 0
			}
		},{
			name: '������ %',
			color:'#A22',
			data:errors,
			tooltip: {
				valueDecimals: 1
			}
		}

		]
	});
});