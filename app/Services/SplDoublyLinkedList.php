<?php
/**
 * Created by PhpStorm.
 * User: fengxin
 * Date: 2019/8/26
 * Time: 5:34 PM
 */
namespace App\Services;
class SplDoublyLinkedList implements \Iterator,\ArrayAccess,\Countable{
    /**
     * @var _llist 定义一个数组用于存放数据
     */
    protected $_llist   = array();

    /**
     * @var _it_mode 链表的迭代模式
     */
    protected $_it_mode = 0;

    /**
     * @var _it_pos 链表指针
     */
    protected $_it_pos  = 0;
    /**
     * 迭代模式
     * @see setIteratorMode
     */
    const IT_MODE_LIFO     = 0x00000002;
    const IT_MODE_FIFO     = 0x00000000;
    const IT_MODE_KEEP     = 0x00000000;
    const IT_MODE_DELETE   = 0x00000001;

    /**
     * @return 返回被移出尾部节点元素
     * @throw RuntimeException 如果链表为空则抛出异常
     */
    public function pop()
    {
        if (count($this->_llist) == 0) {
            throw new RuntimeException("Can't pop from an empty datastructure");
        }
        return array_pop($this->_llist);
    }

    /**
     * @return 返回被移出头部节点元素
     * @throw RuntimeException 如果链表为空则抛出异常
     */
    public function shift()
    {
        if (count($this->_llist) == 0) {
            throw new RuntimeException("Can't shift from an empty datastructure");
        }
        return array_shift($this->_llist);
    }

    /**
     * 往链表尾部添加一个节点元素
     * @param $data 要添加的节点元素
     */
    public function push($data)
    {
        array_push($this->_llist, $data);
        return true;
    }

    /**
     * 往链表头部添加一个节点元素
     * @param $data 要添加的节点元素
     */
    public function unshift($data)
    {
        array_unshift($this->_llist, $data);
        return true;
    }

    /**
     * @return 返回尾部节点元素，并把指针指向尾部节点元素
     */
    public function top()
    {
        return end($this->_llist);
    }

    /**
     * @return 返回头部节点元素，并把指针指向头部节点元素
     */
    public function bottom()
    {
        return reset($this->_llist);
    }

    /**
     * @return 返回链表节点数
     */
    public function count()
    {
        return count($this->_llist);
    }

    /**
     * @return 判断链表是否为空
     */
    public function isEmpty()
    {
        return ($this->count() == 0);
    }
    /**
     * 设置迭代模式
     * - 迭代的顺序 (先进先出、后进先出)
     *  - SplDoublyLnkedList::IT_MODE_LIFO (堆栈)
     *  - SplDoublyLnkedList::IT_MODE_FIFO (队列)
     *
     * - 迭代过程中迭代器的行为
     *  - SplDoublyLnkedList::IT_MODE_DELETE (删除已迭代的节点元素)
     *  - SplDoublyLnkedList::IT_MODE_KEEP   (保留已迭代的节点元素)
     *
     * 默认的模式是 0 : SplDoublyLnkedList::IT_MODE_FIFO | SplDoublyLnkedList::IT_MODE_KEEP
     *
     * @param $mode 新的迭代模式
     */
    public function setIteratorMode($mode)
    {
        $this->_it_mode = $mode;
    }

    /**
     * @return 返回当前的迭代模式
     * @see setIteratorMode
     */
    public function getIteratorMode()
    {
        return $this->_it_mode;
    }

    /**
     * 重置节点指针
     */
    public function rewind()
    {
        if ($this->_it_mode & self::IT_MODE_LIFO) {
            $this->_it_pos = count($this->_llist)-1;
        } else {
            $this->_it_pos = 0;
        }
    }

    /**
     * @return 判断指针对应的节点元素是否存在
     */
    public function valid()
    {
        return array_key_exists($this->_it_pos, $this->_llist);
    }

    /**
     * @return 返回当前指针的偏移位置
     */
    public function key()
    {
        return $this->_it_pos;
    }

    /**
     * @return 返回当前指针对应的节点元素
     */
    public function current()
    {
        return $this->_llist[$this->_it_pos];
    }

    /**
     * 将指针向前移动一个偏移位置
     */
    public function next()
    {
        if ($this->_it_mode & self::IT_MODE_LIFO) {
            if ($this->_it_mode & self::IT_MODE_DELETE) {
                $this->pop();
            }
            $this->_it_pos--;
        } else {
            if ($this->_it_mode & self::IT_MODE_DELETE) {
                $this->shift();
            } else {
                $this->_it_pos++;
            }
        }
    }
    /**
     * @return 偏移位置是否存在
     *
     * @param $offset             偏移位置
     * @throw OutOfRangeException 如果偏移位置超出范围或者无效则抛出异常
     */
    public function offsetExists($offset)
    {
        if (!is_numeric($offset)) {
            throw new OutOfRangeException("Offset invalid or out of range");
        } else {
            return array_key_exists($offset, $this->_llist);
        }
    }

    /**
     * @return 获取偏移位置对应的值
     *
     * @param $offset             偏移位置
     * @throw OutOfRangeException 如果偏移位置超出范围或者无效则抛出异常
     */
    public function offsetGet($offset)
    {
        if ($this->_it_mode & self::IT_MODE_LIFO) {
            $realOffset = count($this->_llist)-$offset;
        } else {
            $realOffset = $offset;
        }
        if (!is_numeric($offset) || !array_key_exists($realOffset, $this->_llist)) {
            throw new OutOfRangeException("Offset invalid or out of range");
        } else {
            return $this->_llist[$realOffset];
        }
    }

    /**
     * @return 设置偏移位置对应的值
     *
     * @param $offset             偏移位置
     * @throw OutOfRangeException 如果偏移位置超出范围或者无效则抛出异常
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            return $this->push($value);
        }
        if ($this->_it_mode & self::IT_MODE_LIFO) {
            $realOffset = count($this->_llist)-$offset;
        } else {
            $realOffset = $offset;
        }
        if (!is_numeric($offset) || !array_key_exists($realOffset, $this->_llist)) {
            throw new OutOfRangeException("Offset invalid or out of range");
        } else {
            $this->_llist[$realOffset] = $value;
        }
    }

    /**
     * @return 删除偏移位置对应的值
     *
     * @param $offset             偏移位置
     * @throw OutOfRangeException 如果偏移位置超出范围或者无效则抛出异常
     */
    public function offsetUnset($offset)
    {
        if ($this->_it_mode & self::IT_MODE_LIFO) {
            $realOffset = count($this->_llist)-$offset;
        } else {
            $realOffset = $offset;
        }
        if (!is_numeric($offset) || !array_key_exists($realOffset, $this->_llist)) {
            throw new OutOfRangeException("Offset invalid or out of range");
        } else {
            array_splice($this->_llist, $realOffset, 1);
        }
    }
    public function list(){
        return $this->_llist;
    }
}