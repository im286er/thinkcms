<?php

namespace app\admin\model;

use think\Exception;

use think\Loader;

use think\Model;

/**
 * 推荐位内容模型
 * @author WeiZeng
 */
class PositionContent extends Model
{
    // 数据完成：新增
    protected $insert = ['create_time'];

    // 数据完成：更新
    protected $update = ['update_time'];

    /**
     * 推荐位获取器
     * @access public
     * @param int $position_id 推荐位id
     * @return string
     */
    public function getPositionIdAttr($position_id)
    {
        // 推荐位名
        $position_name = '';

        // 获取所有推荐位
        $positionModel = Loader::model('Position');
        $positionData  = $positionModel->getPositionAll();

        // 获取推荐位名
        foreach ($positionData as $value) {
            if($value->position_id == $position_id) {
                $position_name = $value->position_name;
            }
        }

        return !empty($position_name) ? $position_name : '未知';
    }

    /**
     * 文章序号获取器
     * @access public
     * @param int $article_id 文章主键
     * @return string
     */
    public function getArticleIdAttr($article_id)
    {
        return $article_id == 0 ? '' : $article_id;
    }

    /**
     * 缩略图获取器
     * @access public
     * @param string $thumb 缩略图
     * @return string
     */
    public function getThumbAttr($thumb)
    {
        return !empty($thumb) ? '有' : '无';
    }

    /**
     * 创建时间获取器
     * @access public
     * @param string $create_time 创建时间
     * @return string
     */
    public function getCreateTimeAttr($create_time)
    {
        return !empty($create_time) ? date('Y-m-d H:i:s', $create_time) : '未知';
    }

    /**
     * 状态获取器
     * @access public
     * @param string $status 状态
     * @return string
     */
    public function getStatusAttr($status)
    {
        return $status == 1 ? '启用' : '禁用';
    }

    /**
     * 创建时间修改器
     * @access public
     * @return int
     */
    public function setCreateTimeAttr()
    {
        return time();
    }

    /**
     * 更新时间修改器
     * @access public
     * @return int
     */
    public function setUpdateTimeAttr()
    {
        return time();
    }

    /**
     * 获取推荐位内容
     * @access public
     * @param int $position_content_id 推荐位内容主键
     * @return object
     */
    public function getPositionContent($position_content_id)
    {
        // 查询记录
        $positionContent = self::get($position_content_id);

        return $positionContent;
    }

    /**
     * 获取轮播图数据
     * @access public
     * @return array
     */
    public function getCarousel()
    {
        // 查询记录
        $carouselData = self::all(function($query) {
            $query->where(['position_id' => 1, 'status' => 1])
                  ->order(['list_order' => 'desc', 'position_content_id' => 'desc'])
                  ->limit(4);
        });

        return $carouselData;
    }

    /**
     * 获取小图推荐数据
     * @access public
     * @return array
     */
    public function getSmallPic()
    {
        // 查询记录
        $smallPicData = self::all(function($query) {
            $query->where(['position_id' => 2, 'status' => 1])
                  ->order(['list_order' => 'desc', 'position_content_id' => 'desc'])
                  ->limit(3);
        });

        return $smallPicData;
    }

    /**
     * 获取广告位数据
     * @access public
     * @return array
     */
    public function getAd()
    {
        // 查询记录
        $adData = self::all(function($query) {
            $query->where(['position_id' => 3, 'status' => 1])
                  ->order(['list_order' => 'desc', 'position_content_id' => 'desc'])
                  ->limit(2);
        });

        return $adData;
    }

    /**
     * 分页
     * @access public
     * @param array $where 查询条件
     * @param array $query 查询参数
     * @return array
     */
    public function positionContentPaginate($where, $query)
    {
        // 获取分页记录
        $pageList = self::where($where)
                    ->order(['list_order' => 'desc', 'position_content_id' => 'desc'])
                    ->paginate(5, false, $query);
        // 获取分页导航
        $pageNav = $pageList->render();

        return ['pageList' => $pageList, 'pageNav' => $pageNav];
    }

    /**
     * 推荐位内容添加
     * @access public
     * @param array $data 添加数据
     * @return bool
     * @throws Exception
     */
    public function positionContentAdd($data)
    {
        // 插入记录
        $result = $this->validate('PositionContent.add')
                  ->allowField(['title', 'position_id', 'thumb', 'address', 'article_id'])
                  ->save($data);
        if($result === false) {
            throw new Exception('添加出错');
        }

        return true;
    }

    /**
     * 推荐位内容排序
     * @access public
     * @param array $data 排序数据
     * @return bool
     * @throws Exception
     */
    public function positionContentSort($data)
    {
        // 批量更新
        $result = $this->validate('PositionContent.sort')->saveAll($data);
        if($result === false) {
            throw new Exception('排序更新出错');
        }

        return true;
    }

    /**
     * 更新状态
     * @access public
     * @param array $data 更新数据
     * @return bool
     * @throws Exception
     */
    public function updateStatus($data)
    {
        // 更新记录
        $result = $this->validate('PositionContent.setStatus')
                  ->save($data, ['position_content_id' => $data['position_content_id']]);
        if($result === false) {
            throw new Exception('更新状态出错');
        }

        return true;
    }

    /**
     * 文章推送方法
     * @access public
     * @param array $data 推送数据
     * @return bool
     * @throws Exception
     */
    public function articlePush($data)
    {
        // 获取文章推送数据
        $pushData     = [];
        $articleModel = Loader::model('Article');

        foreach ($data['pushData']['articleIdData'] as $value) {
            $articlePushData = $articleModel->getPushData($value['article_id']);

            // 判断文章数据是否存在
            if(empty($articlePushData)) {
                throw new Exception('文章数据获取失败');
            }

            $pushData[] = [
                'article_id'  => $value['article_id'],
                'position_id' => $data['pushData']['position_id'],
                'title'       => $articlePushData->title,
                'thumb'       => $articlePushData->getData('thumb'),
                'address'     => $articlePushData->getData('source'),
            ];
        }

        // 插入记录
        $result = $this->saveAll($pushData);
        if($result === false) {
            throw new Exception('插入记录出错');
        }

        return true;
    }

    /**
     * 推荐位内容更新
     * @access public
     * @param array $data 更新数据
     * @return bool
     * @throws Exception
     */
    public function positionContentUpdate($data)
    {
        // 更新记录
        $result = $this->validate('PositionContent.edit')
                  ->allowField(['title', 'position_id', 'thumb', 'address', 'article_id'])
                  ->save($data, ['position_content_id' => $data['position_content_id']]);
        if($result === false) {
            throw new Exception('更新出错');
        }

        return true;
    }
}
