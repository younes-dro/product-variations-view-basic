const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
  entry: {
    settings:'./src/index.js',
    'dro-pvvp-add-variation-images': './src/variation-images/dro-pvvp-add-variation-images.ts',
  },
  output: {
    path: path.resolve(__dirname, 'assets/js/admin'),
    filename: process.env.NODE_ENV === 'production' ? '[name].min.js' : '[name].js',
  },
  module: {
    rules: [
      {
        test: /\.ts?$/,
        exclude: /node_modules/,
        use: 'ts-loader'
      },
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
    extensions: ['.js', '.jsx', '.ts'],
  },
  mode: process.env.NODE_ENV || 'development',
  devtool: process.env.NODE_ENV === 'production' ? false : 'source-map',
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
 * Settings Script for Product Variations View add-on
 * 
 * Author: Younes DRO (younesdro@gmail.com)
 * Date: ${new Date().toLocaleString()}
 * Released under the GPLv2 or later.
 */`,
      raw: true,
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
    }),
  ],
};
