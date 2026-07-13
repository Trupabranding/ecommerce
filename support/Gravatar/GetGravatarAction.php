<?php

declare(strict_types=1);

namespace Support\Gravatar;

use Domain\Access\Admin\Models\Admin;

readonly class GetGravatarAction
{
    public function execute(
        Admin $user,
    ): string {

        // https://docs.gravatar.com/avatars/php/
        return (string) uri('https://api.gravatar.com')
            ->withPath('/avatar/'.hash('sha256', strtolower(trim($user->email))))
            ->withQuery([
                's' => 80,
                'r' => 'g',

                // https://github.dev/leek/filament-dicebear/blob/main/src/DiceBearProvider.php
                // // https://www.dicebear.com/how-to-use/http-api/
                // 'd' => 'https://api.dicebear.com/9.x/pixel-art/png?seed='.$email,

                // https://ui-avatars.com/
                'd' => 'https://i2.wp.com/ui-avatars.com/api/'.
                    implode('/', [
                        urlencode($user->name), // name
                        '80', // size
                        '000000', // background
                        'FFFFFF', // color
                        // '', // length
                        // '', // font-size
                        // '', // rounded
                        // '', // uppercase
                        // '', // bold
                        // '', // format
                    ]),
            ]);
    }
}
