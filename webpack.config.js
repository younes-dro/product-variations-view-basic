const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
  entry: './src/index.js',
  output: {
    path: path.resolve(__dirname, 'assets/js/admin'),
    filename: process.env.NODE_ENV === 'production' ? 'settings.min.js' : 'settings.js',
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react'],
          },
        },
      },
    ],
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  mode: process.env.NODE_ENV || 'development',
  optimization: {
    minimize: process.env.NODE_ENV === 'production',
    minimizer: [
      new TerserPlugin({
        terserOptions: {
          format: {
            comments: /@license|@preserve|^!/i, 
          },
        },
        extractComments: false, 
      }),
    ],
  },
  plugins: [
    new webpack.BannerPlugin({
      banner: `/*! 
 * Settings Script for WooCommerce
 * 
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: ${new Date().toLocaleString()}
 * Released under the GPLv2 or later.
 */`,
      raw: true,
    }),
  ],
};
