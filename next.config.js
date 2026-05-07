/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'export',
  images: {
    unoptimized: true,
  },
  // サブディレクトリ（/ffxivsch）で公開する場合の設定
  basePath: '/ffxivsch',
};

export default nextConfig;