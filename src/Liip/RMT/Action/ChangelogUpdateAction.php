<?php

namespace Liip\RMT\Action;

use Liip\RMT\Changelog\ChangelogManager;
use Liip\RMT\Context;

/**
 * Update the changelog file
 */
class ChangelogUpdateAction extends BaseAction
{
    protected $options;

    public function __construct($options)
    {
        $this->options = array_merge(array(
            'dump-commits' => false,
            'format' => 'simple',
            'file' => 'CHANGELOG'
        ), $options);
    }

    public function execute()
    {
        if ($this->options['dump-commits'] == true) {
            $extraLines = Context::get('vcs')->getAllModificationsSince(
                Context::get('version-persister')->getCurrentVersionTag(),
                false
            );
            $this->options['extra-lines'] = $extraLines;
            unset($this->options['dump-commits']);
        }

        $manager = new ChangelogManager($this->options['file'], $this->options['format']);
        $manager->update(
            Context::getParam('new-version'),
            Context::get('information-collector')->getValueFor('comment'),
            array_merge(
                array('type' => Context::get('information-collector')->getValueFor('type', null)),
                $this->options
            )
        );
        $this->confirmSuccess();
    }

    public function getInformationRequests()
    {
        return array('comment');
    }
}

