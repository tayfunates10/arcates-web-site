<?php
/**
 * Islem gunlugu.
 *
 * Kim, ne zaman, hangi kayit uzerinde ne yapti.  DOCS.md 9.11
 */

declare(strict_types=1);

namespace Arcates\Controllers\Admin;

use Arcates\Core\Request;
use Arcates\Core\Response;
use Arcates\Core\Security;

final class ActivityController extends Controller
{
    protected string $section = 'activity';
    protected ?string $ability = 'activity.view';

    private const PER_PAGE = 50;

    public function index(Request $request, array $params): Response
    {
        if ($guard = $this->guardAdmin()) {
            return $guard;
        }

        $page   = max(1, $request->int('sayfa', 1));
        $offset = ($page - 1) * self::PER_PAGE;

        $filterAction = $request->str('islem');
        $filterUser   = $request->int('kullanici', 0);

        $where  = [];
        $args   = [];

        if ($filterAction !== '') {
            $where[]           = 'l.action = :action';
            $args[':action']   = $filterAction;
        }
        if ($filterUser > 0) {
            $where[]         = 'l.user_id = :user';
            $args[':user']   = $filterUser;
        }

        $clause = $where ? ' WHERE ' . implode(' AND ', $where) : '';

        $total = (int) $this->db()->value('SELECT COUNT(*) FROM activity_log l' . $clause, $args);

        $rows = $this->db()->all(
            'SELECT l.id, l.action, l.entity, l.entity_id, l.detail, l.ip, l.created_at,
                    u.name AS user_name, u.email AS user_email
               FROM activity_log l
               LEFT JOIN users u ON u.id = l.user_id'
            . $clause .
            ' ORDER BY l.created_at DESC, l.id DESC
              LIMIT ' . self::PER_PAGE . ' OFFSET ' . $offset,
            $args
        );

        foreach ($rows as &$row) {
            $row['ip'] = Security::unpackIp($row['ip']);
        }
        unset($row);

        return $this->view('activity', [
            'title'   => 'Islem gunlugu',
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'pages'   => (int) ceil($total / self::PER_PAGE),
            'actions' => $this->db()->column('SELECT DISTINCT action FROM activity_log ORDER BY action'),
            'users'   => $this->db()->all('SELECT id, name FROM users ORDER BY name'),
            'filters' => ['islem' => $filterAction, 'kullanici' => $filterUser],
        ]);
    }
}
