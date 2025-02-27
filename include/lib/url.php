<?php
/**
 * URL
 * @package EMLOG
 * @link https://www.emlog.net
 */

class Url {

	/**
	 * Get article links
	 */
	static function log($blogId) {
		$urlMode = Option::get('isurlrewrite');
		$logUrl = '';
		$CACHE = Cache::getInstance();

		//开启文章别名
		if (Option::get('isalias') == 'y') {
			$logalias_cache = $CACHE->readCache('logalias');
			if (!empty($logalias_cache[$blogId])) {
				$logsort_cache = $CACHE->readCache('logsort');
				$sort = '';
				//分类模式下的url
				if (3 == $urlMode && isset($logsort_cache[$blogId])) {
					$sort = !empty($logsort_cache[$blogId]['alias']) ?
						$logsort_cache[$blogId]['alias'] :
						$logsort_cache[$blogId]['name'];
					$sort .= '/';
				}
				$logUrl = BLOG_URL . $sort . urlencode($logalias_cache[$blogId]);
				//开启别名html后缀
				if (Option::get('isalias_html') == 'y') {
					$logUrl .= '.html';
				}
				return $logUrl;
			}
		}

		switch ($urlMode) {
			case '0'://默认：动态
				$logUrl = BLOG_URL . '?post=' . $blogId;
				break;
			case '1'://静态
				$logUrl = BLOG_URL . 'post-' . $blogId . '.html';
				break;
			case '2'://目录
				$logUrl = BLOG_URL . 'post/' . $blogId;
				break;
			case '3'://分类
				$log_sort = $CACHE->readCache('logsort');
				if (!empty($log_sort[$blogId]['alias'])) {
					$logUrl = BLOG_URL . $log_sort[$blogId]['alias'] . '/' . $blogId;
				} elseif (!empty($log_sort[$blogId]['name'])) {
					$logUrl = BLOG_URL . $log_sort[$blogId]['name'] . '/' . $blogId;
				} else {
					$logUrl = BLOG_URL . $blogId;
				}
				$logUrl .= '.html';
				break;
		}
		return $logUrl;
	}

	static function record($record, $page = null) {
		switch (Option::get('isurlrewrite')) {
			case '0':
				$recordUrl = BLOG_URL . '?record=' . $record;
				if ($page) {
					$recordUrl .= '&page=';
				}
				break;
			default:
				$recordUrl = BLOG_URL . 'record/' . $record;
				if ($page) {
					$recordUrl = BLOG_URL . 'record/' . $record . '/page/';
				}
				break;
		}
		return $recordUrl;
	}

	static function sort($sortId, $page = null) {
		$CACHE = Cache::getInstance();
		$sort_cache = $CACHE->readCache('sort');
		$sort_index = !empty($sort_cache[$sortId]['alias']) ? $sort_cache[$sortId]['alias'] : $sortId;
		switch (Option::get('isurlrewrite')) {
			case '0':
				$sortUrl = BLOG_URL . '?sort=' . $sortId;
				if ($page) {
					$sortUrl .= '&page=';
				}
				break;
			default:
				$sortUrl = BLOG_URL . 'sort/' . $sort_index;
				if ($page) {
					$sortUrl = BLOG_URL . 'sort/' . $sort_index . '/page/';
				}
				break;
		}
		return $sortUrl;
	}

	static function author($authorId, $page = null) {
		switch (Option::get('isurlrewrite')) {
			case '0':
				$authorUrl = BLOG_URL . '?author=' . $authorId;
				if ($page) {
					$authorUrl .= '&page=';
				}
				break;
			default:
				$authorUrl = BLOG_URL . 'author/' . $authorId;
				if ($page) {
					$authorUrl = BLOG_URL . 'author/' . $authorId . '/page/';
				}
				break;
		}
		return $authorUrl;
	}

	static function tag($tag, $page = null) {
		switch (Option::get('isurlrewrite')) {
			case '0':
				$tagUrl = BLOG_URL . '?tag=' . $tag;
				if ($page) {
					$tagUrl .= '&page=';
				}
				break;
			default:
				$tagUrl = BLOG_URL . 'tag/' . $tag;
				if ($page) {
					$tagUrl = BLOG_URL . 'tag/' . $tag . '/page/';
				}
				break;
		}
		return $tagUrl;
	}

	static function logPage() {
		switch (Option::get('isurlrewrite')) {
			case '0':
				$logPageUrl = BLOG_URL . '?page=';
				break;
			default:
				$logPageUrl = BLOG_URL . 'page/';
				break;
		}
		return $logPageUrl;
	}

	static function comment($blogId, $pageId, $cid) {
		$commentUrl = Url::log($blogId);
		if ($pageId > 1) {
			if (Option::get('isurlrewrite') == 0 && strpos($commentUrl, '=') !== false) {
				$commentUrl .= '&comment-page=';
			} else {
				$commentUrl .= '/comment-page-';
			}
			$commentUrl .= $pageId;
		}
		$commentUrl .= '#' . $cid;
		return $commentUrl;
	}

	/**
	 * 获取导航链接
	 */
	static function navi($type, $typeId, $url) {
		switch ($type) {
			case Navi_Model::navitype_custom:
			case Navi_Model::navitype_home:
			case Navi_Model::navitype_t:
			case Navi_Model::navitype_admin:
				break;
			case Navi_Model::navitype_sort:
				$url = self::sort($typeId);
				break;
			case Navi_Model::navitype_page:
				$url = self::log($typeId);
				break;
			default:
				$url = (strpos($url, 'http') === 0 ? '' : BLOG_URL) . $url;
				break;
		}
		return $url;
	}

}
